<?php

namespace App\Http\Controllers;

use App\ProjectRelevanceHistory;
use App\RelevanceHistory;
use App\RelevanceHistoryResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HistoryRelevanceController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        $history = [];
        $main = ProjectRelevanceHistory::where('user_id', '=', Auth::id())->get();
        foreach ($main as $item) {
            foreach ($item->stories as $story) {
                $history[] = $story;
            }
        }

        return view('relevance-analysis.history', [
            'main' => $main,
            'history' => $history,
        ]);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function changeGroupName(Request $request)
    {
        $project = ProjectRelevanceHistory::where('id', '=', $request->id)->first();

        $project->group_name = $request->name;

        $project->save();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function changeCalculateState(Request $request): JsonResponse
    {
        $project = RelevanceHistory::where('id', '=', $request->id)->first();

        $project->calculate = filter_var($request->calculate, FILTER_VALIDATE_BOOLEAN);

        $project->save();

//        $mainHistory = ProjectRelevanceHistory::where('user_id', '=', Auth::id())->get();
//
        $info = ProjectRelevanceHistory::calculateInfo($project->projectRelevanceHistory->stories);

        $project->projectRelevanceHistory->total_points = $info['points'];
        $project->projectRelevanceHistory->count_sites = $info['count'];
        $project->projectRelevanceHistory->save();

        return response()->json([]);
    }


    /**
     * @param int $id
     * @return void
     */
    public function show(int $id)
    {
        $admin = false;
        foreach (Auth::user()->role as $role) {
            if ($role == '1' || $role == '3') {
                $admin = true;
                break;
            }
        }

        return view('relevance-analysis.show-history', ['admin' => $admin, 'id' => $id]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getDetailsInfo(Request $request): JsonResponse
    {
        $history = RelevanceHistoryResult::where('project_id', '=', $request->id)->first();

        return response()->json([
            'history' => $history
        ]);
    }

    public function createQueueView()
    {
        return view('relevance-analysis.queue');
    }

    public function createTaskQueue(Request $request)
    {

    }
}
