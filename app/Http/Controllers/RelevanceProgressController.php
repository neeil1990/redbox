<?php

namespace App\Http\Controllers;

use App\Relevance;
use App\RelevanceHistoryResult;
use App\RelevanceProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RelevanceProgressController extends Controller
{
    public function startProgress(Request $request): JsonResponse
    {
        $request = $request->all();

        if (isset($request['data']['type']) && $request['data']['type'] === 'list')
        {
            $ar = array_diff(explode("\n", $request['data']['siteList']), [""]);
            if (count($ar) < 5)
            {
                return response()->json([
                    'message' => __('The list of sites must contain at least 5 sites')
                ], 415);
            }
        }

        $mainUrl = parse_url($request['data']['link']);
        if(isset($mainUrl['scheme']) === false || isset($mainUrl['host']) === false)
        {
            return response()->json([
                'message' => __('Ваша посадочная страница должна быть полным URL адресом. Пример: https://site.ru')
            ], 415);
        }

        return response()->json([
            'hash' => RelevanceProgress::startProgress()
        ]);
    }

    public function getProgress(Request $request): JsonResponse
    {
        $progress = RelevanceProgress::where('hash', '=', $request->hash)->first();

        if (isset($progress)) {
            if ($progress->error) {
                $progress->delete();

                return response()->json([
                    'crash' => true,
                ]);
            } else if ($progress->progress === 100) {
                $history = RelevanceHistoryResult::where('hash', $request->hash)->first();

                if (isset($history)) {
                    return response()->json([
                        'progress' => $progress->progress,
                        'result' => Relevance::uncompress($history),
                        'id' => $history->id,
                    ]);
                } else {
                    return response()->json([
                        'progress' => 99,
                    ]);
                }
            }
        }

        return response()->json([
            'progress' => $progress->progress ?? 0
        ]);
    }

    public function endProgress(Request $request)
    {
        return RelevanceProgress::endProgress($request->hash);
    }
}
