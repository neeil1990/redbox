<?php

namespace App\Http\Controllers;

use App\Jobs\RelevanceAnalysisQueue;
use App\ProjectRelevanceHistory;
use App\Relevance;
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
        $config = RelevanceAnalysisConfig::first();
        $tags = RelevanceTags::where('user_id', '=', Auth::id())->get();
        $projects = ProjectRelevanceHistory::where('user_id', '=', Auth::id())->get();
        $admin = User::isUserAdmin();

        return view('relevance-analysis.history', [
            'main' => $projects,
            'admin' => $admin,
            'config' => $config,
            'tags' => $tags,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getStories(Request $request): JsonResponse
    {
        $history = ProjectRelevanceHistory::where('id', '=', $request->history_id)->first();
        $admin = User::isUserAdmin();
        $userId = Auth::id();

        $share = RelevanceSharing::where('user_id', '=', $userId)
            ->where('owner_id', '=', $history->user_id)
            ->where('access', '=', 2)
            ->first();

        if ($history->user_id != $userId && !isset($share) && !$admin) {
            return response()->json([
                'success' => false,
                'message' => __("You don't have access to this object"),
                'code' => 415
            ]);
        }

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

            $admin = User::isUserAdmin();
            $ownerId = $history->mainHistory->mainHistory->user_id;
            $userId = Auth::id();

            $share = RelevanceSharing::where('user_id', '=', $userId)
                ->where('owner_id', '=', $ownerId)
                ->where('access', '=', 2)
                ->first();

            if ($ownerId != $userId && !isset($share) && !$admin) {
                return response()->json([
                    'success' => false,
                    'message' => __("You don't have access to this object"),
                    'code' => 415
                ]);
            } elseif (!$history->compressed) {
                foreach ($history->getOriginal() as $key => $item) {
                    if ($key != 'id' && $key != 'project_id' && $key != 'created_at' && $key != 'updated_at') {
                        $history[$key] = base64_encode(gzcompress($item, 9));
                    }
                }

                $history->compressed = true;
                $history->save();

                $history = RelevanceHistoryResult::where('project_id', '=', $request->id)->latest('updated_at')->first();
            }
            $history = Relevance::uncompressed($history);

        } catch (\Throwable $exception) {
            return response()->json([
                'code' => 415,
                'message' => __('The data was lost')
            ]);
        }

        return response()->json([
            'code' => 200,
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
        $admin = User::isUserAdmin();
        $userId = Auth::id();
        $object = RelevanceHistory::where('id', '=', $request->id)->first();
        $ownerId = $object->mainHistory->user_id;

        $share = RelevanceSharing::where('user_id', '=', $userId)
            ->where('owner_id', '=', $ownerId)
            ->where('access', '=', 2)
            ->first();

        if ($ownerId != $userId && !isset($share) && !$admin) {
            return response()->json([
                'success' => false,
                'message' => __("You don't have access to this object"),
                'code' => 415
            ]);
        } else if ($object->state == 1 || $object->state == -1) {
            $object->state = 0;
            $object->save();

            RelevanceAnalysisQueue::dispatch(
                $ownerId,
                $request->all(),
                $request['id']
            );
        }
        return response()->json([
            'success' => true,
            'code' => 200
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatQueueCompetitorsScan(Request $request): JsonResponse
    {
        $admin = User::isUserAdmin();
        $userId = Auth::id();
        $object = RelevanceHistory::where('id', '=', $request->id)->first();
        $ownerId = $object->mainHistory->user_id;

        $share = RelevanceSharing::where('user_id', '=', $userId)
            ->where('owner_id', '=', $ownerId)
            ->where('access', '=', 2)
            ->first();

        if ($ownerId != $userId && !isset($share) && !$admin) {
            return response()->json([
                'success' => false,
                'message' => __("You don't have access to this object"),
                'code' => 415
            ]);
        } else if ($object->state == 1 || $object->state == -1) {
            $object->state = 0;
            $object->save();

            RelevanceAnalysisQueue::dispatch(
                $ownerId,
                $request->all(),
                $request['id'],
                false,
                false,
                'competitors'
            );
        }
        return response()->json([
            'success' => true,
            'code' => 200
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatQueueMainPageScan(Request $request): JsonResponse
    {
        $admin = User::isUserAdmin();
        $userId = Auth::id();
        $object = RelevanceHistory::where('id', '=', $request->id)->first();
        $ownerId = $object->mainHistory->user_id;

        $share = RelevanceSharing::where('user_id', '=', $userId)
            ->where('owner_id', '=', $ownerId)
            ->where('access', '=', 2)
            ->first();

        if ($ownerId != $userId && !isset($share) && !$admin) {
            return response()->json([
                'success' => false,
                'message' => __("You don't have access to this object"),
                'code' => 415
            ]);
        } else if ($object->state == 1 || $object->state == -1) {
            $object->state = 0;
            $object->save();

            RelevanceAnalysisQueue::dispatch(
                $ownerId,
                $request->all(),
                $request['id'],
                false,
                false,
                'mainPage'
            );
        }
        return response()->json([
            'success' => true,
            'code' => 200
        ]);
    }

    /**
     * @param RelevanceHistory $object
     * @return JsonResponse
     */
    public function getHistoryInfo(RelevanceHistory $object): JsonResponse
    {
        $userId = Auth::id();
        $ownerId = $object->mainHistory->user_id;
        $admin = User::isUserAdmin();
        $share = RelevanceSharing::where('user_id', '=', $userId)
            ->where('owner_id', '=', $ownerId)
            ->where('access', '=', 2)
            ->first();

        if ($ownerId != $userId && !isset($share) && !$admin) {
            return response()->json([
                'success' => false,
                'message' => __("You don't have access to this object"),
                'code' => 415
            ]);
        }

        return response()->json([
            'history' => json_decode($object->request)
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getHistoryInfoV2(Request $request): JsonResponse
    {
        $projects = RelevanceHistory::where('project_relevance_history_id', '=', $request->historyId)->latest('id')->get();
        $userId = Auth::id();
        $ownerId = $projects[0]->user_id;
        $admin = User::isUserAdmin();
        $share = RelevanceSharing::where('user_id', '=', $userId)
            ->where('owner_id', '=', $ownerId)
            ->where('access', '=', 2)
            ->first();

        if ($ownerId != $userId && !isset($share) && !$admin) {
            return response()->json([
                'success' => false,
                'message' => __("You don't have access to this object"),
                'code' => 415
            ]);
        }

        $responseObject = [];
        foreach ($projects as $project) {
            $responseObject[$project->phrase][] = $project->toArray();
        }

        return response()->json([
            'object' => $responseObject
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeEmptyResults(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $main = ProjectRelevanceHistory::where('id', '=', $request->id)->first();
        $admin = User::isUserAdmin();
        $share = RelevanceSharing::where('user_id', '=', $userId)
            ->where('owner_id', '=', $main->user_id)
            ->where('access', '=', 2)
            ->first();

        if ($main->user_id != $userId && !isset($share) && !$admin) {
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
                RelevanceHistory::where('comment', '=', '')
                    ->where('main_link', '=', $link->main_link)
                    ->where('phrase', '=', $link->phrase)
                    ->where('region', '=', $link->region)
                    ->where('project_relevance_history_id', '=', $request->id)
                    ->delete();
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
        $userId = Auth::id();
        $main = ProjectRelevanceHistory::where('id', '=', $request->id)->first();
        $admin = User::isUserAdmin();
        $share = RelevanceSharing::where('user_id', '=', $userId)
            ->where('owner_id', '=', $main->user_id)
            ->where('access', '=', 2)
            ->first();

        if ($main->user_id != $userId && !isset($share) && !$admin) {
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    function checkQueueScanState(Request $request): JsonResponse
    {
        $project = RelevanceHistory::where('id', '=', $request->id)->first();

        if ($project->state == 1) {
            $newProject = RelevanceHistory::where('id', '!=', $request->id)
                ->where('id', '>', $request->id)
                ->where('user_id', '=', Auth::id())
                ->latest('id')
                ->first();
            return response()->json([
                'message' => 'success',
                'newProject' => $newProject
            ]);
        }

        return response()->json([
            'message' => 'wait',
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatScanUniqueSites(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $project = ProjectRelevanceHistory::where('id', '=', $request->id)->first();
        $admin = User::isUserAdmin();
        $share = RelevanceSharing::where('user_id', '=', $userId)
            ->where('owner_id', '=', $project->user_id)
            ->where('access', '=', 2)
            ->first();

        if ($project->user_id != $userId && !isset($share) && !$admin) {
            return response()->json([
                'success' => false,
                'code' => 415,
                'message' => __("You don't have access to this object")
            ]);
        }
        $items = RelevanceHistory::where('project_relevance_history_id', '=', $request->id)
            ->distinct(['main_link', 'phrase', 'region'])
            ->get(['main_link', 'phrase', 'region']);

        $ids = [];
        foreach ($items as $item) {
            $record = RelevanceHistory::where('main_link', '=', $item->main_link)
                ->where('project_relevance_history_id', '=', $request->id)
                ->where('phrase', '=', $item->phrase)
                ->where('region', '=', $item->region)
                ->where('calculate', '=', 1)
                ->latest('last_check')
                ->first();

            if ($record->state != 0) {
                RelevanceAnalysisQueue::dispatch(
                    $project->user_id,
                    json_decode($record->request, true),
                    $record->id
                );

                $record->state = 0;
                $record->save();

                $ids[] = $record->id;
            }

        }

        return response()->json([
            'success' => false,
            'code' => 200,
            'message' => __('Your tasks have been successfully added to the queue'),
            'object' => $ids,
        ]);
    }
}
