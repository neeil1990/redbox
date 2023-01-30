<?php

namespace App\Http\Controllers;

use App\ProjectRelevanceHistory;
use App\Relevance;
use App\RelevanceProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RelevanceProgressController extends Controller
{
    /**
     * @return mixed|string
     */
    public function startProgress()
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

        if ($progress->progress === 100) {
            $project = ProjectRelevanceHistory::where('user_id', '=', Auth::id())->latest('updated_at')->first();
            $history = \App\RelevanceHistoryResult::where('project_id', '=', $project->id)->latest('updated_at')->first();
            return response()->json([
                'progress' => $progress->progress,
                'result' => Relevance::uncompressed($history)
            ]);
        }

        return response()->json([
            'progress' => $progress->progress
        ]);
    }

    public function endProgress(Request $request)
    {
        return RelevanceProgress::endProgress($request->hash);
    }
}
