<?php

namespace App\Http\Controllers;

use App\ProjectRelevanceHistory;
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
    public function startProgress(): JsonResponse
    {
        $progress = new RelevanceProgress();
        $progress->user_id = Auth::id();
        $progress->hash = md5(Auth::id() . time());
        $progress->progress = 0;

        $progress->save();

        return response()->json([
            'hash' => $progress->hash
        ]);
    }

    public function getProgress(Request $request): JsonResponse
    {
        $progress = RelevanceProgress::where('hash', '=', $request->hash)->first();

        if (isset($progress) && $progress->progress === 100) {
            $project = RelevanceHistory::where('user_id', '=', Auth::id())->latest('created_at')->first();
            $history = RelevanceHistoryResult::where('project_id', '=', $project->id)->latest('updated_at')->first();
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
