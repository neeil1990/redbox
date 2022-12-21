<?php

namespace App\Http\Controllers;

use App\Models\Relevance\RelevanceProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * @param $hash
     * @return JsonResponse
     */
    public function getProgress(Request $request): JsonResponse
    {
        return response()->json([
            'progress' => RelevanceProgress::where('hash', '=', $request->hash)->first()
        ]);
    }

    /**
     * @param $hash
     * @return mixed
     */
    public function endProgress(Request $request)
    {
        return RelevanceProgress::endProgress($request->hash);
    }
}
