<?php

namespace App\Http\Controllers;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Relevance;
use App\RelevanceAnalyseResults;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RelevanceController extends Controller
{
    public function index()
    {
        return view('relevance-analysis.index');
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

            $relevance = new Relevance($request);
            $relevance->getMainPageHtml($request->link);
            $relevance->removeIgnoredDomains($request->count, $request->ignoredDomains, $xmlResponse['response']['results']['grouping']['group']);
            $relevance->parseXmlResponse();
            $relevance->analysis($request);
            $relevance->params->save();
            return RelevanceController::prepareResponse($relevance, $request);

        } catch (\Exception $e) {
            self::logError($request, $e);

            return response()->json()->setStatusCode(500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatMainPageAnalysis(Request $request): JsonResponse
    {
        try {
            $relevance = new Relevance($request);
            $params = RelevanceAnalyseResults::where('user_id', '=', Auth::id())->first();
            $relevance->getMainPageHtml($request->link);
            $relevance->setSites($params->sites);
            $relevance->setPages($params->html_relevance);

            $relevance->analysis($request);
            $relevance->params->save();

            return RelevanceController::prepareResponse($relevance, $request);
        } catch (\Exception $e) {
            self::logError($request, $e);

            return response()->json()->setStatusCode(500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatRelevanceAnalysis(Request $request): JsonResponse
    {
        try {
            $params = RelevanceAnalyseResults::where('user_id', '=', Auth::id())->first();
            $xml = new SimplifiedXmlFacade(20, $request->region);
            $xml->setQuery($request->phrase);
            $xmlResponse = $xml->getXMLResponse();

            $relevance = new Relevance($request);
            $relevance->setMainPage($params->html_main_page);
            $relevance->removeIgnoredDomains($request->count, $request->ignoredDomains, $xmlResponse['response']['results']['grouping']['group']);
            $relevance->parseXmlResponse();
            $relevance->analysis($request);
            $relevance->params->save();

            return RelevanceController::prepareResponse($relevance, $request);
        } catch (\Exception $e) {
            self::logError($request, $e);

            return response()->json()->setStatusCode(500);
        }
    }

    /**
     * @param $relevance
     * @param $request
     * @return JsonResponse
     */
    public function prepareResponse($relevance, $request): JsonResponse
    {
//        $avgSpaces = TextLengthController::countingSpaces($text) / $request->count - 1;
//        $countSpaces = TextLengthController::countingSpaces($mainPageText) - 1;

        $count = count($relevance->sites);
        $text =
            $relevance->competitorsText . ' ' .
            $relevance->competitorsLinks;
        $avgLength = Str::length($text) / $count;
        $avgCountWords = TextLengthController::countingWord($text) / $count;

        $mainPageText =
            $relevance->mainPage['html'] . ' ' .
            $relevance->mainPage['linkText'] . ' ' .
            $relevance->mainPage['hiddenText'];

        $length = Str::length($mainPageText);
        $countWords = TextLengthController::countingWord($mainPageText);

        return response()->json([
            'clouds' => [
                'competitorsTextAndLinksCloud' => $relevance->competitorsTextAndLinksCloud,
                'competitorsLinksCloud' => $relevance->competitorsLinksCloud,
                'competitorsTextCloud' => $relevance->competitorsTextCloud,
                'mainPageTextWithLinksCloud' => $relevance->mainPage['textWithLinksCloud'],
                'mainPageLinksCloud' => $relevance->mainPage['linksCloud'],
                'mainPageTextCloud' => $relevance->mainPage['textCloud'],
            ],
            'unigramTable' => $relevance->wordForms,
            'sites' => $relevance->sites,
            'avg' => [
                'countWords' => $avgCountWords,
//                'countSpaces' => $avgSpaces,
                'countSymbols' => $avgLength,
//                'countSymbolsWithoutSpaces' => $avgLength - $avgSpaces
            ],
            'mainPage' => [
                'countWords' => $countWords,
//                'countSpaces' => $countSpaces,
                'countSymbols' => $length,
//                'countSymbolsWithoutSpaces' => $length - $countSpaces
            ]
        ]);
    }

    public function logError($request, $e)
    {
        Log::debug('repeat relevance scan error', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'request' => $request->all()
        ]);
    }

}
