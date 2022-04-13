<?php

namespace App\Http\Controllers;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Relevance;
use App\RelevanceAnalyseResults;
use App\TestRelevance;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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

        $messages = [
            'link.required' => __('A link to the landing page is required.'),
            'phrase.required_without' => __('The keyword is required to fill in.'),
            'siteList.required_without' => __('The list of sites is required to fill in.'),
        ];

        $request->validate([
            'link' => 'required|website',
            'phrase' => 'required_without:siteList|not_website',
            'siteList' => 'required_without:link',
        ], $messages);


        $relevance = new TestRelevance($request->input('link'));
        $relevance->getMainPageHtml();

        if ($request->input('type') === 'list') {

            $sitesList = str_replace("\r\n", "\n", $request->input('siteList'));
            $sitesList = explode("\n", $sitesList);
            Log::debug('21312', [count($sitesList)]);

            if (count($sitesList) <= 7) {
                return response()->json([
                    'countError' => 'Список сайов должен содержать минимум 7 сайтов'
                ], 500);
            }

            foreach ($sitesList as $item) {
                $relevance->domains[] = str_replace('www.', "", mb_strtolower(trim($item)));
            }

        } else {
            $xml = new SimplifiedXmlFacade(20, $request->input('region'));
            $xml->setQuery($request->input('phrase'));
            $xmlResponse = $xml->getXMLResponse();

            $relevance->removeIgnoredDomains(
                $request->input('count'),
                $request->input('ignoredDomains'),
                $xmlResponse['response']['results']['grouping']['group']
            );
        }
        $relevance->parseSites();
        $relevance->analysis($request);


        return RelevanceController::successResponse($relevance);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function configureChildrenRows(Request $request): JsonResponse
    {
        $filename = md5(microtime(true));
        $filePath = public_path('children/' . $filename . '.json');
        $wordForms = json_decode($request->sessionStorage, true);

        $result = [];
        foreach ($wordForms as $wordForm) {
            foreach ($wordForm as $keyword => $word) {
                if ($keyword != 'total') {
                    $result[$keyword] = $word;
                }
            }
        }
        if (File::put($filePath, json_encode($result, JSON_UNESCAPED_UNICODE))) {
            return response()->json([
                'filename' => $filename
            ], 201);
        } else {
            return response()->json([
                'message' => 'Файл не создан'
            ], 500);
        }
    }

    /**
     * @param $fileName
     * @return array|false|Application|Factory|View|mixed
     */
    public function showChildrenRows($fileName)
    {
        $filePath = public_path('children/' . $fileName . '.json');

        $file = File::get($filePath);
        $array = json_decode($file, true);

        return view('relevance-analysis.children', ['array' => $array]);
    }

    /**
     * @param $relevance
     * @return JsonResponse
     */
    public function successResponse($relevance): JsonResponse
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
            'tfCompClouds' => $relevance->tfCompClouds,
            //new functions
            'phrases' => $relevance->phrases ?? null
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
