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
        $though->result = json_decode(gzuncompress(base64_decode($though->result)), true);
        $allResult = $though->result;
        $though->result = array_slice($though->result, 0, count($though->result) / 20);
        $count = count($though->result);

        return view('relevance-analysis.though.show', [
            'though' => $though,
            'allElems' => $allResult,
            'allCount' => count($allResult),
            'count' => $count
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getSliceResult(Request $request): JsonResponse
    {
        $record = ProjectRelevanceThough::where('id', '=', $request->id)->first();

        $array = json_decode(gzuncompress(base64_decode($record->result)), true);
        $sliceArray = array_slice($array, $request->count, count($array) / 20);

        return response()->json([
            'elems' => $sliceArray,
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
        if (count($items) == 0) {
            return response()->json([
                'code' => 415,
                'message' => 'Не удалось получить требуемые данные, возможно вам стоит перезапустить анализ проекта'
            ]);
        }

        $though = ProjectRelevanceThough::firstOrNew([
            'project_relevance_history_id' => $request->id,
        ]);

        $though->state = 0;

        $though->save();

        dispatch(new RelevanceThoughAnalysisQueue([
            'items' => $items->toArray(),
            'mainId' => $request->id,
            'thoughId' => $though->id,
            'countRecords' => count($items),
            'stage' => 1,
        ]));

        return response()->json([
            'success' => false,
            'code' => 200,
            'message' => "Сквозной анализ успешно добавлен в очередь",
        ]);
    }
}
