<?php

namespace App\Http\Controllers;

use App\Jobs\RelevanceAnalysisQueue;
use App\ProjectRelevanceHistory;
use App\RelevanceAnalysisConfig;
use App\RelevanceHistory;
use App\RelevanceHistoryResult;
use App\RelevanceSharing;
use App\RelevanceTags;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class HistoryRelevanceController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        $tags = RelevanceTags::where('user_id', '=', Auth::id())->get();
        $config = RelevanceAnalysisConfig::first();
        $projects = ProjectRelevanceHistory::where('user_id', '=', Auth::id())->get();
        $admin = User::isUserAdmin();

        return view('relevance-analysis.history', [
            'main' => $projects,
            'admin' => $admin,
            'config' => $config,
            'tags' => $tags
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getStories(Request $request): JsonResponse
    {
        $history = ProjectRelevanceHistory::where('id', '=', $request->history_id)->first();

        return response()->json([
            'stories' => $history->stories
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function editGroupName(Request $request): JsonResponse
    {
        $project = ProjectRelevanceHistory::where('id', '=', $request->id)->first();

        $project->group_name = $request->name;

        $project->save();

        return response()->json([]);
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

        $info = ProjectRelevanceHistory::calculateInfo($project->projectRelevanceHistory->stories);

        $project->projectRelevanceHistory->total_points = $info['points'];
        $project->projectRelevanceHistory->count_sites = $info['count'];
        $project->projectRelevanceHistory->save();

        return response()->json([]);
    }

    /**
     * @param int $id
     * @return View|void
     */
    public function show(int $id)
    {
        $object = RelevanceHistory::where('id', '=', $id)->first();

        $access = RelevanceSharing::where('user_id', '=', Auth::id())
            ->where('project_id', '=', $object->project_relevance_history_id)
            ->first();

        if (!isset($access) && $object->projectRelevanceHistory->user_id != Auth::id()) {
            return abort(403, __("You don't have access to this object"));
        }

        $admin = User::isUserAdmin();

        $object->request = json_decode($object->request, true);
        return view('relevance-analysis.show-history', [
            'admin' => $admin,
            'id' => $id,
            'object' => $object,
            'access' => $access ?? null
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getDetailsInfo(Request $request): JsonResponse
    {
        $history = RelevanceHistoryResult::where('project_id', '=', $request->id)->latest('updated_at')->first();
        $history = json_decode($history, true);

        $clouds_competitors = json_decode($history['clouds_competitors'], true);
        $clouds_main_page = json_decode($history['clouds_main_page'], true);
        $avg = json_decode($history['avg'], true);
        $main_page = json_decode($history['main_page'], true);

        $history = [
            'clouds_competitors' => [
                'totalTf' => json_decode($clouds_competitors['totalTf'], true),
                'textTf' => json_decode($clouds_competitors['textTf'], true),
                'linkTf' => json_decode($clouds_competitors['linkTf'], true),

                'textAndLinks' => json_decode($clouds_competitors['textAndLinks'], true),
                'links' => json_decode($clouds_competitors['links'], true),
                'text' => json_decode($clouds_competitors['text'], true),
            ],
            'clouds_main_page' => [
                'totalTf' => json_decode($clouds_main_page['totalTf'], true),
                'textTf' => json_decode($clouds_main_page['textTf'], true),
                'linkTf' => json_decode($clouds_main_page['linkTf'], true),
                'textWithLinks' => json_decode($clouds_main_page['textWithLinks'], true),
                'links' => json_decode($clouds_main_page['links'], true),
                'text' => json_decode($clouds_main_page['text'], true),
            ],
            'avg' => [
                'countWords' => json_decode($avg['countWords'], true),
                'countSymbols' => json_decode($avg['countSymbols'], true),
            ],
            'main_page' => [
                'countWords' => json_decode($main_page['countWords'], true),
                'countSymbols' => json_decode($main_page['countSymbols'], true),
            ],
            'unigram_table' => json_decode($history['unigram_table'], true),
            'sites' => json_decode($history['sites'], true),
            'tf_comp_clouds' => json_decode($history['tf_comp_clouds'], true),
            'phrases' => json_decode($history['phrases'], true),
            'avg_coverage_percent' => json_decode($history['avg_coverage_percent'], true),
            'recommendations' => json_decode($history['recommendations'], true),
        ];

        return response()->json([
            'history' => $history,
            'config' => RelevanceAnalysisConfig::first(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function editComment(Request $request): JsonResponse
    {
        $project = RelevanceHistory::where('id', '=', $request->id)->first();

        $project->comment = $request->comment;

        $project->save();

        return response()->json([]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatScan(Request $request): JsonResponse
    {
        $object = RelevanceHistory::where('id', '=', $request->id)->first();
        if ($object->state == 1 || $object->state == -1) {
            $object->state = 0;
            $object->save();

            RelevanceAnalysisQueue::dispatch(
                Auth::id(),
                $request->all(),
                $request['id']
            );
            return response()->json([]);
        }

        return response()->json([], 500);
    }

    /**
     * @param RelevanceHistory $object
     * @return JsonResponse
     */
    public function getHistoryInfo(RelevanceHistory $object): JsonResponse
    {
        return response()->json([
            'history' => json_decode($object->request)
        ]);
    }
}
