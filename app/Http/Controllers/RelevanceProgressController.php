<?php

namespace App\Http\Controllers;

use App\Relevance;
use App\RelevanceHistory;
use App\RelevanceHistoryResult;
use App\RelevanceProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RelevanceProgressController extends Controller
{
    public function startProgress(Request $request): JsonResponse
    {
        $request = $request->all();
        if (isset($request['data']['type']) && $request['data']['type'] === 'list') {
            $ar = array_diff(explode("\n", $request['data']['siteList']), [""]);
            if (count($ar) < 5) {
                return response()->json([
                    'message' => __('The list of sites must contain at least 5 sites')
                ], 415);
            }
        }

        return response()->json([
            'hash' => RelevanceProgress::startProgress()
        ]);
    }

    public function getProgress(Request $request): JsonResponse
    {
        $progress = RelevanceProgress::where('hash', '=', $request->hash)->first();

        if (isset($progress) && $progress->progress === 100) {
            Log::debug('hash', [$request->hash]);
            $history = RelevanceHistoryResult::where('hash', '=', $request->hash)->first();
            Log::debug('hash', [$history]);

            return response()->json([
                'progress' => $progress->progress,
                'result' => Relevance::uncompress($history)
            ]);
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
