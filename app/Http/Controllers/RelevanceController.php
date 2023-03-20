<?php

namespace App\Http\Controllers;

use App\Jobs\Relevance\RelevanceAnalyseQueue;
use App\Queue;
use App\Relevance;
use App\RelevanceAnalyseResults;
use App\RelevanceAnalysisConfig;
use App\RelevanceHistory;
use App\RelevanceProgress;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RelevanceController extends Controller
{
    public function index(): View
    {
        $admin = User::isUserAdmin();
        $config = RelevanceAnalysisConfig::first();

        return view('relevance-analysis.index', ['admin' => $admin, 'config' => $config]);
    }

    public function analyse(Request $request): JsonResponse
    {
        if (RelevanceHistory::checkRelevanceAnalysisLimits()) {
            return response()->json([
                'code' => 415,
                'message' => __('Your limits are exhausted this month')
            ]);
        }

        $request->validate([
            'link' => 'required|website',
            'phrase' => 'required|not_website',
        ], [
            'link.required' => __('A link to the landing page is required.'),
            'phrase.required' => __('The keyword is required to fill in.'),
        ]);

        RelevanceAnalyseQueue::dispatch($request->all(), $request->input('exp'), Auth::id(), 'full');

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Повторный анализ конкурентов с использованием html посадочной страницы, которая была получена во время прошлого запроса
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatRelevanceAnalysis(Request $request): JsonResponse
    {
        if (RelevanceHistory::checkRelevanceAnalysisLimits()) {
            return response()->json([
                'code' => 415,
                'message' => __('Your limits are exhausted this month')
            ]);
        }

        $messages = [
            'link.required' => __('A link to the landing page is required.'),
        ];

        $request->validate([
            'link' => 'required|website',
        ], $messages);

        RelevanceAnalyseQueue::dispatch($request->all(), false, Auth::id(), 'competitors');

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Парсинг посадочной страницы и забираем данные конкурентов полученые во время прошлого сканирования
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatMainPageAnalysis(Request $request): JsonResponse
    {
        if (RelevanceHistory::checkRelevanceAnalysisLimits()) {
            return response()->json([
                'code' => 415,
                'message' => __('Your limits are exhausted this month')
            ]);
        }

        $messages = [
            'link.required' => __('A link to the landing page is required.'),
        ];

        $request->validate([
            'link' => 'required|website',
        ], $messages);

        RelevanceAnalyseQueue::dispatch($request->all(), false, Auth::id(), 'main');

        return response()->json([
            'success' => true
        ]);
    }

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
            'history_id' => $relevance->params['result_id'],
            'sites' => $relevance->sites,
            'sitesAVG' => $relevance->avg,
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
            'searchPassages' => $relevance->request['searchPassages'] ?? false
        ];

        return response()->json($result);
    }

    public function createQueue(): View
    {
        $config = RelevanceAnalysisConfig::first();
        $admin = User::isUserAdmin();

        return view('relevance-analysis.queue', [
            'config' => $config,
            'admin' => $admin,
        ]);
    }

    public function createTaskQueue(Request $request): JsonResponse
    {
        $rows = explode("\n", $request->params);
        if (RelevanceHistory::checkRelevanceAnalysisLimits(count($rows))) {
            return response()->json([
                'code' => 415,
                'message' => __('Your limits are exhausted this month')
            ]);
        }

        foreach ($rows as $row) {
            Queue::addInQueue($row, $request);
        }

        return response()->json([
            'code' => 200
        ]);
    }

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

    public function removePageHistory(Request $request)
    {
        RelevanceAnalyseResults::where('user_id', '=', Auth::id())
            ->where('page_hash', '=', $request['pageHash'])
            ->delete();
    }

}
