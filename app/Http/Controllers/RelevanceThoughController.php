<?php

namespace App\Http\Controllers;

use App\Jobs\RelevanceThoughAnalysisQueue;
use App\ProjectRelevanceThough;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RelevanceThoughController extends Controller
{
    /**
     * @param ProjectRelevanceThough $though
     * @return View
     */
    public function show(ProjectRelevanceThough $though): View
    {
        $though->result = gzuncompress(base64_decode($though->result));

        return view('relevance-analysis.though.show', [
            'though' => $though,
            'microtime' => microtime(true),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function startThroughAnalyse(Request $request): JsonResponse
    {
        HistoryRelevanceController::checkAccess($request);
        $items = HistoryRelevanceController::getUniqueScanned($request->id);
        Log::debug('id', [$request->id]);
        Log::debug('items', [$items]);
        Log::debug('unique items', [count($items)]);
        if (count($items) == 0) {
            return response()->json([
                'code' => 415,
                'message' => 'Не удалось получить требуемые данные, возможно вам стоит перезапустить анализ проекта'
            ]);
        }

        dispatch(new RelevanceThoughAnalysisQueue($items, $request->id));

        return response()->json([
            'success' => false,
            'code' => 200,
            'message' => "Сквозной анализ успешно добавлен в очередь",
        ]);
    }
}
