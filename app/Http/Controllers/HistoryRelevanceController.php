<?php

namespace App\Http\Controllers;

use App\Common;
use App\Exports\RelevanceStatisticsExport;
use App\Jobs\Relevance\RelevanceHistoryQueue;
use App\ProjectRelevanceHistory;
use App\ProjectRelevanceThough;
use App\Relevance;
use App\RelevanceAnalysisConfig;
use App\RelevanceHistory;
use App\RelevanceHistoryResult;
use App\RelevanceSharing;
use App\RelevanceTags;
use App\User;
use App\UsersJobs;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class HistoryRelevanceController extends Controller
{
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

        $results = $history->stories()->get([
            'phrase', 'main_link', 'region',
            'last_check', 'points', 'position',
            'coverage', 'coverage_tf', 'density',
            'width', 'density', 'calculate',
            'id', 'project_relevance_history_id',
            'comment', 'user_id', 'state',
        ]);

        foreach ($results as $result) {
            if (isset($result->results['average_values'])) {
                $result['average_values'] = json_decode($result->results['average_values']);
            }
            unset($result->results);
        }

        return response()->json([
            'stories' => $results
        ]);
    }

    public function editGroupName(Request $request): JsonResponse
    {
        $project = ProjectRelevanceHistory::where('id', '=', $request->id)->first();

        $project->group_name = $request->name;

        $project->save();

        return response()->json([]);
    }

    public function changeCalculateState(Request $request): JsonResponse
    {
        $project = RelevanceHistory::where('id', '=', $request->id)->first();

        $project->calculate = filter_var($request->calculate, FILTER_VALIDATE_BOOLEAN);

        $project->save();

        ProjectRelevanceHistory::calculateInfo($project->projectRelevanceHistory);

        return response()->json([]);
    }

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

        } catch (Throwable $exception) {
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

    public function editComment(Request $request): JsonResponse
    {
        $project = RelevanceHistory::where('id', '=', $request->id)->first();

        $project->comment = $request->comment;

        $project->save();

        return response()->json([]);
    }

    public function repeatScan(Request $request): JsonResponse
    {
        if (RelevanceHistory::checkRelevanceAnalysisLimits()) {
            return response()->json([
                'code' => 415,
                'message' => __('Your limits are exhausted this month')
            ]);
        }

        $this->validate($request, [
            'phrase' => 'required',
            'link' => 'required',
        ]);

        $admin = User::isUserAdmin();
        $userId = Auth::id();
        $object = RelevanceHistory::where('id', '=', $request->id)->with('mainHistory')->first();
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

            RelevanceHistoryQueue::dispatch(
                $ownerId,
                $request->all(),
                $request['id']
            )->onQueue(UsersJobs::getPriority($ownerId))->onConnection('database');
        }
        return response()->json([
            'success' => true,
            'code' => 200
        ]);
    }

    public function repeatQueueCompetitorsScan(Request $request): JsonResponse
    {
        if (RelevanceHistory::checkRelevanceAnalysisLimits()) {
            return response()->json([
                'code' => 415,
                'message' => __('Your limits are exhausted this month')
            ]);
        }

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

            RelevanceHistoryQueue::dispatch(
                $ownerId,
                $request->all(),
                $request->id,
                false,
                false,
                'competitors'
            )->onQueue(UsersJobs::getPriority($ownerId))->onConnection('database');
        }
        return response()->json([
            'success' => true,
            'code' => 200
        ]);
    }

    public function repeatQueueMainPageScan(Request $request): JsonResponse
    {
        if (RelevanceHistory::checkRelevanceAnalysisLimits()) {
            return response()->json([
                'code' => 415,
                'message' => __('Your limits are exhausted this month')
            ]);
        }

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

            RelevanceHistoryQueue::dispatch(
                $ownerId,
                $request->all(),
                $request['id'],
                false,
                false,
                'mainPage'
            )->onQueue(UsersJobs::getPriority($ownerId))->onConnection('database');
        }
        return response()->json([
            'success' => true,
            'code' => 200
        ]);
    }

    public function getHistoryInfo(RelevanceHistory $object): JsonResponse
    {
        $userId = Auth::id();
        $ownerId = $object->user_id;
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

    public function getHistoryInfoV2(Request $request): JsonResponse
    {
        $projects = RelevanceHistory::where('project_relevance_history_id', '=', $request->historyId)->latest('id')
            ->get([
                'id',
                'created_at',
                'region',
                'main_link',
                'points',
                'position',
                'user_id',
                'coverage',
                'coverage_tf',
                'width',
                'density',
                'phrase',
                'state',
                'comment'
            ]);

        $ownerId = $projects[0]->user_id;
        $admin = User::isUserAdmin();
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
        }

        $responseObject = [];
        foreach ($projects as $project) {
            $responseObject[$project->phrase][] = $project->toArray();
        }

        return response()->json([
            'object' => $responseObject
        ]);
    }

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

        $items = $this->getUniqueScanned($request->id);

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
        $removed = ProjectRelevanceHistory::where('id', '=', $request->id)
            ->where('count_sites', '=', 0)->delete();

        return response()->json([
            'success' => true,
            'message' => __('Success'),
            'points' => $info['points'],
            'countSites' => $info['count'],
            'countChecks' => $info['countChecks'],
            'avgPosition' => $info['avgPosition'],
            'objectId' => $request->id,
            'removed' => $removed,
            'code' => 200
        ]);
    }

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

        $info = ProjectRelevanceHistory::calculateInfo($main);
        $removed = ProjectRelevanceHistory::where('id', '=', $request->id)
            ->where('count_sites', '=', 0)->delete();

        return response()->json([
            'success' => true,
            'message' => __('It was deleted') . ' ' . $count . ' ' . __('projects'),
            'points' => $info['points'],
            'countSites' => $info['count'],
            'countChecks' => $info['countChecks'],
            'avgPosition' => $info['avgPosition'],
            'objectId' => $request->id,
            'removed' => $removed,
            'code' => 200
        ]);
    }

    function checkQueueScanState(Request $request): JsonResponse
    {
        $project = RelevanceHistory::where('id', '=', $request->id)->first();

        if ($project->state == 1) {
            $newProject = RelevanceHistory::where('id', '!=', $request->id)
                ->where('id', '>', $request->id)
                ->where('user_id', '=', $project->user_id)
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

    public function repeatScanUniqueSites(Request $request): JsonResponse
    {
        $ownerId = $this->checkAccess($request);
        $items = $this->getUniqueScanned($request->id);

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
                RelevanceHistoryQueue::dispatch(
                    $ownerId,
                    json_decode($record->request, true),
                    $record->id
                )->onQueue(UsersJobs::getPriority($ownerId))->onConnection('database');

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

    public static function checkAccess($request)
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

        return $project->user_id;
    }

    public static function getUniqueScanned($id): Collection
    {
        return RelevanceHistory::where('project_relevance_history_id', '=', $id)
            ->distinct(['main_link', 'phrase', 'region'])
            ->get(['main_link', 'phrase', 'region']);
    }

    public function rescanProjects(Request $request): JsonResponse
    {
        $admin = User::isUserAdmin();
        $userId = Auth::id();

        foreach (json_decode($request->ids, true) as $id) {
            $object = RelevanceHistory::where('id', '=', $id)->with('mainHistory')->first();
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

                RelevanceHistoryQueue::dispatch(
                    $ownerId,
                    json_decode($object->request, true),
                    $id
                )->onQueue(UsersJobs::getPriority($ownerId))->onConnection('database');
            }

            ProjectRelevanceThough::where('id', '=', $request->thoughId)->update(['cleaning_state' => 1]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Проекты успешно добавлены в очередь на повторный анализ',
            'code' => 200
        ]);
    }

    function checkAnalyseProgress(Request $request): JsonResponse
    {
        $object = RelevanceHistory::where('id', '=', $request->id)->first();

        if ($object->state == 0) {
            return response()->json([
                'message' => 'wait',
                'code' => 200
            ]);
        } else if ($object->state == -1) {
            return response()->json([
                'message' => 'error',
                'code' => 200
            ]);
        }

        try {
            return response()->json([
                'message' => 'success',
                'object' => $object->results->id,
                'code' => 200,
                'newObject' => RelevanceHistory::where('project_relevance_history_id', '=', $object->project_relevance_history_id)->latest()->first(),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'error',
                'code' => 500,
            ]);
        }

    }

    public function showMissingWords(RelevanceHistoryResult $result)
    {
        $admin = User::isUserAdmin();
        $wordForms = json_decode(gzuncompress(base64_decode($result->unigram_table)), true);

        $result = [];
        foreach ($wordForms as $wordForm) {
            $key = array_key_first($wordForm);
            $elem = $wordForm[$key];

            if ($elem['repeatInLinkMainPage'] == 0 && $elem['repeatInTextMainPage'] == 0) {
                $result[$key] = $elem;
            }
        }

        return view('relevance-analysis.scan-result.missing-words', [
            'result' => $result,
            'admin' => $admin
        ]);
    }

    public function showChildrenRows(RelevanceHistoryResult $result): View
    {
        $admin = User::isUserAdmin();
        $wordForms = json_decode(gzuncompress(base64_decode($result->unigram_table)), true);

        $result = [];
        foreach ($wordForms as $wordForm) {
            foreach ($wordForm as $keyword => $word) {
                if ($keyword != 'total') {
                    $result[$keyword] = $word;
                }
            }
        }

        return view('relevance-analysis.scan-result.child-words', [
            'result' => $result,
            'admin' => $admin
        ]);
    }

    public function getFile(int $id, string $type)
    {
        $history = ProjectRelevanceHistory::where('id', '=', $id)->first();
        $admin = User::isUserAdmin();
        $userId = Auth::id();

        $share = RelevanceSharing::where('user_id', '=', $userId)
            ->where('owner_id', '=', $history->user_id)
            ->where('access', '=', 2)
            ->first();

        if ($history->user_id != $userId && !isset($share) && !$admin) {
            abort(403);
        }

        $file = Excel::download(new RelevanceStatisticsExport($id), 'relevance_statistics.' . $type);
        Common::fileExport($file, $type, 'relevance_statistics');
    }
}
