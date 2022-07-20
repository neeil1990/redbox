<?php

namespace App\Http\Controllers;

use App\Queue;
use App\Relevance;
use App\RelevanceAnalyseResults;
use App\RelevanceAnalysisConfig;
use App\RelevanceProgress;
use App\User;
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
        $admin = User::isUserAdmin();
        $config = RelevanceAnalysisConfig::first();

        return view('relevance-analysis.index', ['admin' => $admin, 'config' => $config]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function analysis(Request $request): JsonResponse
    {
        $messages = [
            'link.required' => __('A link to the landing page is required.'),
            'phrase.required' => __('The keyword is required to fill in.'),
            'siteList.required' => __('The list of sites is required to fill in.'),
        ];

        $request->validate([
            'link' => 'required|website',
            'phrase' => 'required_without:siteList|not_website',
            'siteList' => 'required_without:link',
        ], $messages);

        $relevance = new Relevance($request->all());
        $relevance->getMainPageHtml();

        if ($request['type'] == 'phrase') {
            $relevance->analysisByPhrase($request->all(), $request->exp);

        } elseif ($request['type'] == 'list') {
            $relevance->analysisByList($request->all());
        }

        $relevance->analysis(Auth::id());

        return RelevanceController::successResponse($relevance);
    }

    /**
     * Повторный анализ конкурентов с использованием html посадочной страницы, которая была получена во время прошлого запроса
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatRelevanceAnalysis(Request $request): JsonResponse
    {
        RelevanceProgress::editProgress(10, $request);
        $messages = [
            'link.required' => __('A link to the landing page is required.'),
        ];

        $request->validate([
            'link' => 'required|website',
        ], $messages);

        $params = RelevanceAnalyseResults::where('user_id', '=', Auth::id())
            ->where('page_hash', '=', $request['pageHash'])
            ->first();
        $relevance = new Relevance($request->all());
        $relevance->setMainPage($params->html_main_page);
        $relevance->setDomains($params->sites);
        $relevance->parseSites();
        $relevance->analysis(Auth::id());

        return RelevanceController::successResponse($relevance);
    }

    /**
     * Парсинг посадочной страницы и забираем данные конкурентов полученые во время прошлого сканирования
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatMainPageAnalysis(Request $request): JsonResponse
    {
        RelevanceProgress::editProgress(10, $request);
        $messages = [
            'link.required' => __('A link to the landing page is required.'),
        ];

        $request->validate([
            'link' => 'required|website',
        ], $messages);

        $params = RelevanceAnalyseResults::where('user_id', '=', Auth::id())
            ->where('page_hash', '=', $request['pageHash'])
            ->first();
        $relevance = new Relevance($request->all());
        $relevance->getMainPageHtml();
        RelevanceProgress::editProgress(15, $request);
        $relevance->setSites($params->sites);
        $relevance->analysis(Auth::id());

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
    public static function successResponse($relevance): JsonResponse
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
        $admin = User::isUserAdmin();

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
     * @return void
     */
    public function removePageHistory(Request $request)
    {
        RelevanceAnalyseResults::where('user_id', '=', Auth::id())
            ->where('page_hash', '=', $request['pageHash'])
            ->delete();

        Log::debug("У пользователя " . Auth::id() . " была отчищена история сканирования");
    }

}
