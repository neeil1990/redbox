<?php

namespace App\Http\Controllers;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Relevance;
use App\RelevanceAnalyseResults;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RelevanceController extends Controller
{
    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function index()
    {
        return view('relevance-analysis.index');
    }

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function testView()
    {
        return view('relevance-analysis.test');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function analysis(Request $request): JsonResponse
    {
        try {
            $xml = new SimplifiedXmlFacade(20, $request->region);
            $xml->setQuery($request->phrase);
            $xmlResponse = $xml->getXMLResponse();

            $relevance = new Relevance($request->link);
            $relevance->getMainPageHtml();
            $relevance->removeIgnoredDomains(
                $request->count,
                $request->ignoredDomains,
                $xmlResponse['response']['results']['grouping']['group']
            );
            $relevance->parseSites();
            $relevance->analysis($request);
            return RelevanceController::successResponse($relevance);
        } catch (Exception $e) {
            return RelevanceController::errorResponse($request, $e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatMainPageAnalysis(Request $request): JsonResponse
    {
        try {
            $relevance = new Relevance($request->link);
            $params = RelevanceAnalyseResults::where('user_id', '=', Auth::id())->first();
            $relevance->getMainPageHtml();
            $relevance->setSites($params->sites);
            $relevance->setPages($params->html_relevance);
            $relevance->analysis($request);
            return RelevanceController::successResponse($relevance);
        } catch (Exception $e) {
            return RelevanceController::errorResponse($request, $e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatRelevanceAnalysis(Request $request): JsonResponse
    {
        try {
            $relevance = new Relevance($request->link);
            $params = RelevanceAnalyseResults::where('user_id', '=', Auth::id())->first();
            $relevance->setMainPage($params->html_main_page);
            $relevance->setDomains($params->sites);
            $relevance->parseSites();
            $relevance->analysis($request);
            return RelevanceController::successResponse($relevance);
        } catch (Exception $e) {
            return RelevanceController::errorResponse($request, $e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function testAnalyse(Request $request): JsonResponse
    {
        $xml = new SimplifiedXmlFacade(20, $request->region);
        $xml->setQuery($request->phrase);
        $xmlResponse = $xml->getXMLResponse();

        $relevance = new Relevance($request->link);
        $relevance->getMainPageHtml();
        $relevance->removeIgnoredDomains(
            $request->count,
            $request->ignoredDomains,
            $xmlResponse['response']['results']['grouping']['group']
        );
        $relevance->parseSites();
        $relevance->analysis($request);
//        $relevance->maxWordLength = $request->separator;
//        $relevance->removeNoIndex($request->noIndex);
//        $relevance->getHiddenData($request->hiddenText);
//        $relevance->separateLinksFromText();
//        $relevance->removePartsOfSpeech($request->conjunctionsPrepositionsPronouns);
//        $relevance->removeListWords($request);
//        $relevance->deleteEverythingExceptCharacters();
//        $relevance->getTextFromCompetitors();
//        $relevance->separateAllText();
//        $relevance->searchWordForms();
//        $relevance->processingOfGeneralInformation();
//        $relevance->prepareClouds();
//        $relevance->prepareUnigramTable();
//        $relevance->calculateCoverage($request->link);
        $tfCompClouds = [];
        foreach ($relevance->pages as $key => $page) {
            $tfCompClouds[$key] = $relevance->prepareTfCloud($relevance->separateText($page['html'] . ' ' . $page['linkText']));
        }

        return RelevanceController::successResponse($relevance, $tfCompClouds);
    }

    /**
     * @param $relevance
     * @param null $tfCompClouds
     * @return JsonResponse
     */
    public function successResponse($relevance, $tfCompClouds = null): JsonResponse
    {
        $count = count($relevance->sites);
        $text = Relevance::concatenation([$relevance->competitorsText, $relevance->competitorsLinks]);
        $avgCountWords = TextLengthController::countingWord($text) / $count;
        $mainPageText = Relevance::concatenation([
            $relevance->mainPage['html'],
            $relevance->mainPage['linkText'],
            $relevance->mainPage['hiddenText']
        ]);

        return response()->json([
            'clouds' => [
                'competitors' => [
                    'totalTf' => $relevance->competitorsCloud['totalTf'],
                    'textTf' => $relevance->competitorsCloud['textTf'],
                    'linkTf' => $relevance->competitorsCloud['linkTf'],

                    'textAndLinks' => $relevance->competitorsTextAndLinksCloud,
                    'links' => $relevance->competitorsLinksCloud,
                    'text' => $relevance->competitorsTextCloud,
                ],
                'mainPage' => [
                    'totalTf' => $relevance->mainPage['totalTf'],
                    'textTf' => $relevance->mainPage['textTf'],
                    'linkTf' => $relevance->mainPage['linkTf'],
                    'textWithLinks' => $relevance->mainPage['textWithLinks'],
                    'links' => $relevance->mainPage['links'],
                    'text' => $relevance->mainPage['text'],
                ]
            ],
            'avg' => [
                'countWords' => $avgCountWords,
                'countSymbols' => Str::length($text) / $count,
            ],
            'mainPage' => [
                'countWords' => TextLengthController::countingWord($mainPageText),
                'countSymbols' => Str::length($mainPageText),
            ],
            'unigramTable' => $relevance->wordForms,
            'sites' => $relevance->sites,
            'tfCompClouds' => $tfCompClouds ?? null,
            'coverageInfo' => [
                '200' => $relevance->coverageInfo['total200'],
                '600' => $relevance->coverageInfo['total600'],
            ],
        ]);
    }

    /**
     * @param $request
     * @param $e
     * @return JsonResponse
     */
    public static function errorResponse($request, $e): JsonResponse
    {
        Log::debug('relevance scan error', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'request' => $request->all()
        ]);

        return response()->json()->setStatusCode(500);
    }
}
