<?php

namespace App\Http\Controllers;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Queue;
use App\Relevance;
use App\RelevanceAnalyseResults;
use App\RelevanceAnalysisConfig;
use App\TestRelevance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RelevanceController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        $admin = false;
        foreach (Auth::user()->role as $role) {
            if ($role == '1' || $role == '3') {
                $admin = true;
                break;
            }
        }

        $config = RelevanceAnalysisConfig::first();

        return view('relevance-analysis.index', ['admin' => $admin, 'config' => $config]);
    }

    /**
     * @return View
     */
    public function testView(): View
    {
        $config = RelevanceAnalysisConfig::first();
        $admin = false;
        foreach (Auth::user()->role as $role) {
            if ($role == '1' || $role == '3') {
                $admin = true;
                break;
            }
        }

        return view('relevance-analysis.test', [
            'admin' => $admin,
            'config' => $config,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function analysis(Request $request): JsonResponse
    {
        $relevance = new Relevance($request->input('link'), $request->input('separator'));

        $messages = [
            'link.required' => __('A link to the landing page is required.'),
            'phrase.required' => __('The keyword is required to fill in.'),
            'siteList.required' => __('The list of sites is required to fill in.'),
        ];

        if ($request->input('type') === 'phrase') {
            $request->validate([
                'link' => 'required|website',
                'phrase' => 'required',
            ], $messages);
        } else {
            $request->validate([
                'link' => 'required|website',
                'siteList' => 'required',
            ], $messages);

            $sitesList = str_replace("\r\n", "\n", $request->input('siteList'));
            $sitesList = explode("\n", $sitesList);

            foreach ($sitesList as $item) {
                $relevance->domains[] = [
                    'item' => str_replace('www.', '', mb_strtolower(trim($item))),
                    'ignored' => false,
                    'positi3131on' => count($relevance->domains) + 1
                ];
            }
        }

        $relevance->getMainPageHtml();

        if ($request->input('type') === 'phrase') {
            $xml = new SimplifiedXmlFacade($request->input('region'));
            $xml->setQuery($request->input('phrase'));
            $xmlResponse = $xml->getXMLResponse();

            $relevance->removeIgnoredDomains(
                $request->input('count'),
                $request->input('ignoredDomains'),
                $xmlResponse
            );

        }
        $relevance->parseSites($xmlResponse);
        $relevance->analysis($request);

        return RelevanceController::successResponse($relevance);
    }

    /**
     * Повторный анализ конкурентов с использованием html посадочной страницы, которая была получена во время прошлого запроса
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatRelevanceAnalysis(Request $request): JsonResponse
    {
        $messages = [
            'link.required' => __('A link to the landing page is required.'),
        ];

        $request->validate([
            'link' => 'required|website',
        ], $messages);

        $params = RelevanceAnalyseResults::where('user_id', '=', Auth::id())->first();
        $relevance = new Relevance($request->input('link'), $request->input('separator'));
        $relevance->setMainPage($params->html_main_page);
        $relevance->setDomains($params->sites);
        $relevance->parseSites();
        $relevance->analysis($request);

        return RelevanceController::successResponse($relevance);
    }

    /**
     * Парсинг посадочной страницы и забираем данные конкурентов полученые во время прошлого сканирования
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatMainPageAnalysis(Request $request): JsonResponse
    {
        $messages = [
            'link.required' => __('A link to the landing page is required.'),
        ];

        $request->validate([
            'link' => 'required|website',
        ], $messages);

        $params = RelevanceAnalyseResults::where('user_id', '=', Auth::id())->first();
        $relevance = new Relevance($request->input('link'), $request->input('separator'));
        $relevance->getMainPageHtml();
        $relevance->setSites($params->sites);
        $relevance->analysis($request);

        return RelevanceController::successResponse($relevance);
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

        $relevance = new TestRelevance($request->input('link'), $request->input('phrase'), $request->input('separator'));
        $relevance->getMainPageHtml();

        if ($request['type'] == 'phrase') {
            $relevance->analysisByPhrase($request->all());

        } elseif ($request['type'] == 'list') {
            $relevance->analysisByList($request['siteList']);
        }

        $relevance->analysis($request->all(), Auth::id());

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
     * @return View
     */
    public function showChildrenRows($fileName): View
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
        $config = RelevanceAnalysisConfig::first();

        $result = [
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
                'countWords' => $relevance->countWords / $relevance->countNotIgnoredSites,
                'countSymbols' => $relevance->countSymbols / $relevance->countNotIgnoredSites,
            ],
            'mainPage' => [
                'countWords' => $relevance->countWordsInMyPage,
                'countSymbols' => $relevance->countSymbolsInMyPage,
            ],
            'unigramTable' => $relevance->wordForms,
            'sites' => $relevance->sites,
            'tfCompClouds' => $relevance->tfCompClouds,
            'phrases' => $relevance->phrases,
            'avgCoveragePercent' => $relevance->avgCoveragePercent ?? null,
            'recommendations' => $relevance->recommendations ?? null,
            'ltp_count' => $config->ltp_count,
            'ltps_count' => $config->ltps_count,
            'recommendations_count' => $config->recommendations_count,
            'scanned_sites_count' => $config->scanned_sites_count,
            'hide_ignored_domains' => $config->hide_ignored_domains,
            'boostPercent' => $config->boostPercent,
        ];

        return response()->json($result);
    }

    /**
     * @return View
     */
    public function createQueue(): View
    {
        $config = RelevanceAnalysisConfig::first();
        $admin = false;
        foreach (Auth::user()->role as $role) {
            if ($role == '1' || $role == '3') {
                $admin = true;
                break;
            }
        }

        return view('relevance-analysis.queue', [
            'config' => $config,
            'admin' => $admin,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createTaskQueue(Request $request): JsonResponse
    {
        $rows = explode("\n", $request->params);
        foreach ($rows as $row) {
            Queue::addInQueue($row, $request);
        }

        return response()->json([]);
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

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function changeConfig(Request $request): RedirectResponse
    {
        $config = RelevanceAnalysisConfig::first();
        if (!$config) {
            $config = new RelevanceAnalysisConfig();
        }

        $config->count_sites = $request->count;
        $config->region = $request->region;
        $config->ignored_domains = $request->ignored_domains;
        $config->separator = $request->separator;

        $config->noindex = $request->noindex;
        $config->meta_tags = $request->meta_tags;
        $config->parts_of_speech = $request->parts_of_speech;
        $config->remove_my_list_words = $request->remove_my_list_words;
        $config->my_list_words = $request->my_list_words;
        $config->hide_ignored_domains = $request->hide_ignored_domains;

        $config->ltp_count = $request->ltp_count;
        $config->ltps_count = $request->ltps_count;
        $config->scanned_sites_count = $request->scanned_sites_count;
        $config->recommendations_count = $request->recommendations_count;

        $config->boostPercent = $request->boostPercent;

        $config->save();

        return Redirect::back();
    }
}
