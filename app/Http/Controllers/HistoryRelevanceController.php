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
use Carbon\Carbon;
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

        ProjectRelevanceHistory::calculateInfo($project->projectRelevanceHistory);

        return response()->json([]);
    }

    /**
     * @param int $id
     * @return View|void
     */
    public function show(int $id)
    {
        $admin = User::isUserAdmin();
        $object = RelevanceHistory::where('id', '=', $id)->first();

        if (!isset($object)) {
            return abort(404);
        }

        $access = RelevanceSharing::where('user_id', '=', Auth::id())
            ->where('project_id', '=', $object->project_relevance_history_id)
            ->first();

        if (!isset($access) && $object->projectRelevanceHistory->user_id != Auth::id() && !$admin) {
            return abort(403, __("You don't have access to this object"));
        }

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
        try {
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
                'code' => 200,
                'history' => $history,
                'config' => RelevanceAnalysisConfig::first(),
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'code' => 415,
                'message' => __('The data was lost')
            ]);
        }

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
        $admin = User::isUserAdmin();
        $object = RelevanceHistory::where('id', '=', $request->id)->first();
        $userId = Auth::id();
        $ownerId = $object->mainHistory->user_id;

        $share = RelevanceSharing::where('user_id', '=', $userId)
            ->where('owner_id', '=', $ownerId)
            ->first();

        if ($ownerId == $userId || isset($share) || $admin) {
            if ($object->state == 1 || $object->state == -1) {
                $object->state = 0;
                $object->save();

                RelevanceAnalysisQueue::dispatch(
                    $ownerId,
                    $request->all(),
                    $request['id']
                );
                return response()->json([]);
            }
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeEmptyResults(Request $request): JsonResponse
    {
        $main = ProjectRelevanceHistory::where('id', '=', $request->id)->first();
        $admin = User::isUserAdmin();

        if ($main->user_id != Auth::id() && !$admin) {
            return response()->json([
                'success' => false,
                'message' => __("You don't have access to this object"),
                'code' => 415
            ]);
        }

        $items = RelevanceHistory::where('project_relevance_history_id', '=', $request->id)
            ->distinct(['main_link', 'phrase', 'region'])
            ->get(['main_link', 'phrase', 'region']);

        foreach ($items as $link) {
            $records = RelevanceHistory::where('comment', '!=', '')
                ->where('main_link', '=', $link->main_link)
                ->where('phrase', '=', $link->phrase)
                ->where('region', '=', $link->region)
                ->where('project_relevance_history_id', '=', $request->id)
                ->latest('last_check')
                ->get();
            if (count($records) >= 1) {
                $count = RelevanceHistory::where('comment', '=', '')
                    ->where('main_link', '=', $link->main_link)
                    ->where('phrase', '=', $link->phrase)
                    ->where('region', '=', $link->region)
                    ->where('project_relevance_history_id', '=', $request->id)
                    ->delete();
                Log::debug('Чистка проектов без фильтров 1 сценарий', [
                    'user' => Auth::id(),
                    'count' => $count,
                    'time' => Carbon::now()->toDateString()
                ]);
            } else {
                $records = RelevanceHistory::where('comment', '=', '')
                    ->where('main_link', '=', $link->main_link)
                    ->where('phrase', '=', $link->phrase)
                    ->where('region', '=', $link->region)
                    ->where('project_relevance_history_id', '=', $request->id)
                    ->latest('last_check')
                    ->get();
                $iterator = 0;
                foreach ($records as $key => $record) {
                    if ($key != array_key_first($records->toArray())) {
                        $iterator++;
                        $record->delete();
                    }
                }
                Log::debug('Чистка проектов без фильтров 2 сценарий', [
                    'user' => Auth::id(),
                    'count' => $iterator,
                    'time' => Carbon::now()->toDateString()
                ]);
            }
        }

        $info = ProjectRelevanceHistory::calculateInfo($main);

        return response()->json([
            'success' => true,
            'message' => __('Success'),
            'points' => $info['points'],
            'countSites' => $info['count'],
            'countChecks' => $info['countChecks'],
            'avgPosition' => $info['avgPosition'],
            'objectId' => $request->id,
            'code' => 200
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeEmptyResultsFilters(Request $request): JsonResponse
    {
        $main = ProjectRelevanceHistory::where('id', '=', $request->id)->first();
        $admin = User::isUserAdmin();

        if ($main->user_id != Auth::id() && !$admin) {
            return response()->json([
                'success' => false,
                'message' => __("You don't have access to this object"),
                'code' => 415
            ]);
        }

        $query = RelevanceHistory::where('project_relevance_history_id', '=', $request->id);

        if (isset($request->positionAfter)) {
            $query->where('position', '>=', $request->positionAfter);
        }

        if (isset($request->positionBefore)) {
            $query->where('position', '<=', $request->positionBefore);
        }

        if (isset($request->after)) {
            $query->where('last_check', '>=', $request->after);
        }

        if (isset($request->before)) {
            $query->where('last_check', '<=', $request->before);
        }

        if (isset($request->comment)) {
            $query->where('comment', '=', $request->comment);
        }

        if (isset($request->phrase)) {
            $query->where('phrase', '=', $request->phrase);
        }

        if ($request->region === 'all') {
            $query->where('region', '!=', 0);
        } elseif ($request->region !== 'none') {
            $query->where('region', '=', $request->region);
        }

        if (isset($request->link)) {
            $query->where('main_link', '=', $request->link);
        }

        $count = $query->delete();
        Log::debug('Чистка проектов c фильтрами', [
            'user' => Auth::id(),
            'count' => $count,
            'time' => Carbon::now()->toDateString()
        ]);
        $info = ProjectRelevanceHistory::calculateInfo($main);

        return response()->json([
            'success' => true,
            'message' => __('It was deleted') . ' ' . $count . ' ' . __('projects'),
            'points' => $info['points'],
            'countSites' => $info['count'],
            'countChecks' => $info['countChecks'],
            'avgPosition' => $info['avgPosition'],
            'objectId' => $request->id,
            'code' => 200
        ]);
    }
}
