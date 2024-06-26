<?php

namespace App\Http\Controllers;

use App\Jobs\Relevance\RelevanceAnalyseQueue;
use App\Queue;
use App\RelevanceAnalyseResults;
use App\RelevanceAnalysisConfig;
use App\RelevanceHistory;
use App\User;
use App\UsersJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RelevanceController extends Controller
{
    const HIGH_QUEUE = 'relevance_high_priority';
    const MEDIUM_QUEUE = 'relevance_medium_priority';
    const NORMAL_QUEUE = 'relevance_normal_priority';

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
            'phrase' => 'required|not_website|max:50',
        ], [
            'link.required' => __('A link to the landing page is required.'),
            'phrase.required' => __('The keyword is required to fill in.'),
            'phrase.max' => __('Maximum keyword length') . ' 50 ' . __('symbols'),
        ]);

        try {
            RelevanceAnalyseQueue::dispatch($request->all(), $request->input('exp'), Auth::id(), 'full')
                ->onQueue($request->input('queue', self::HIGH_QUEUE))
                ->onConnection('database');
        } catch (\Throwable $e) {
            var_dump($e);
        }

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

        RelevanceAnalyseQueue::dispatch($request->all(), false, Auth::id(), 'competitors')
            ->onQueue(UsersJobs::getPriority(Auth::id()))
            ->onConnection('database');

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

        RelevanceAnalyseQueue::dispatch($request->all(), false, Auth::id(), 'main')
            ->onQueue(UsersJobs::getPriority(Auth::id()))
            ->onConnection('database');

        return response()->json([
            'success' => true
        ]);
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

        $counter = 0;
        foreach ($rows as $row) {
            $counter += Queue::addInQueue($row, $request);
        }

        UsersJobs::updateOrCreate(
            ['user_id' => Auth::id()],
            ['count_jobs' => DB::raw('count_jobs + ' . $counter)]
        );

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
