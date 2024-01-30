<?php

namespace App\Http\Controllers;

use App\ChecklistNotification;
use App\CheckListProjectLabels;
use App\CheckLists;
use App\CheckListsLabels;
use App\ChecklistStubs;
use App\ChecklistTasks;
use App\Classes\SimpleHtmlDom\HtmlDocument;
use App\DomainMonitoring;
use App\MetaTag;
use App\MonitoringDataTableColumnsProject;
use App\ProjectRelevanceHistory;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CheckListController extends Controller
{
    public function index()
    {
        $labels = CheckListsLabels::where('user_id', Auth::id())->get();

        return view('checklist.index', compact('labels'));
    }

    public function tasks(CheckLists $checklist)
    {
        $host = parse_url($checklist->url)['host'];
        $labels = $checklist->labels->toArray();
        $checklist = $this->confirmArray([$checklist]);
        $allLabels = CheckListsLabels::where('user_id', Auth::id())->get()->toArray();

        return view('checklist.tasks', compact('checklist', 'host', 'labels', 'allLabels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required',
        ], [
            'url.required' => __('A link to the landing page is required.'),
        ]);

        if (!preg_match("~^(?:f|ht)tps?://~i", $request->input('url'))) {
            $fullUrl = "https://" . $request->input('url');
        } else {
            $fullUrl = $request->input('url');
        }

        if (CheckLists::where('user_id', Auth::id())->where('url', $fullUrl)->count() > 0) {
            return response()->json([
                'errors' => ['URL' => 'У вас уже есть проект с таким URL']
            ], 422);
        }

        DB::beginTransaction();
        try {
            $client = new Client();
            $response = $client->get($fullUrl);
            if ($response->getStatusCode() === 200) {
                $icon = $this->findIcon($response->getBody()->getContents());

                $project = CheckLists::create([
                    'user_id' => Auth::id(),
                    'icon' => $this->saveIcon($icon, $fullUrl),
                    'url' => $fullUrl,
                ]);

                $this->createSubTasks($request->input('tasks'), $project->id);

                if ($request->input('saveStub') === 'all') {
                    $tree = $this->configureStubs($project->id);
                    $data = [];

                    $data[] = [
                        'user_id' => Auth::id(),
                        'tree' => $tree,
                        'type' => 'personal',
                        'checklist_id' => $request->input('dynamicStub') == 1 ? $project->id : null,
                    ];

                    $data[] = [
                        'user_id' => Auth::id(),
                        'tree' => $tree,
                        'type' => 'classic',
                        'checklist_id' => $request->input('dynamicStub') == 1 ? $project->id : null,
                    ];

                    ChecklistStubs::insert($data);

                } else if ($request->input('saveStub') !== 'no') {
                    $tree = $this->configureStubs($project->id);

                    $data = [
                        'user_id' => Auth::id(),
                        'tree' => $tree,
                        'type' => $request->input('saveStub'),
                    ];

                    if ($request->input('dynamicStub') == 1) {
                        $data['checklist_id'] = $project->id;
                    }

                    ChecklistStubs::create($data);
                }
            }

            DB::commit();
        } catch (Throwable $e) {
            Log::debug('error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            DB::rollback();

            return response()->json([
                'errors' => [
                    $e->getMessage()
                ]
            ], 422);
        }

        return response()->json([
            'message' => __('Success')
        ], 201);
    }

    public function update(Request $request): string
    {
        $this->createSubTasks($request->input('tasks'), $request->input('projectID'), $request->input('parentTask'));

        ChecklistStubs::where('checklist_id', $request->input('projectID'))
            ->update([
                'tree' => $this->configureStubs($request->input('projectID'))
            ]);

        return 'Успешно';
    }

    public function storeStub(Request $request): string
    {
        if ($request->input('action') === 'all') {
            ChecklistStubs::create([
                'user_id' => Auth::id(),
                'name' => $request->input('name'),
                'tree' => json_encode($request->input('stubs')),
                'type' => 'classic',
            ]);

            ChecklistStubs::create([
                'user_id' => Auth::id(),
                'name' => $request->input('name'),
                'tree' => json_encode($request->input('stubs')),
                'type' => 'personal',
            ]);
        } else {
            ChecklistStubs::create([
                'user_id' => Auth::id(),
                'name' => $request->input('name'),
                'tree' => json_encode($request->input('stubs')),
                'type' => $request->input('action'),
            ]);
        }

        return 'Успешно';
    }

    public function editStub(Request $request): JsonResponse
    {
        ChecklistStubs::where('id', $request->input('id'))
            ->update([
                'name' => $request->input('name')
            ]);

        return response()->json([]);
    }

    public function getChecklists(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $sql = CheckLists::where('user_id', $userId)
            ->where('archive', 0);

        $labelName = $request->input('label_name');

        if ($labelName) {
            $labels = CheckListsLabels::where('user_id', $userId)
                ->where('name', 'like', "%$labelName%")
                ->with('checklists')
                ->get();

            $projectIds = [];

            foreach ($labels as $label) {
                $projects = $label->checklists;
                foreach ($projects as $project) {
                    $projectIds[] = $project->id;
                }
            }

            $sql = $sql->whereIn('id', $projectIds);
        }

        if (isset($request->url)) {
            $sql->where('url', 'like', "%$request->url%");
        }

        $lists = $sql->skip($request->input('skip', 0))
            ->take($request->input('countOnPage', 3))
            ->with('tasks:project_id,status,active_after')
            ->with('labels')
            ->get(['icon', 'url', 'id'])
            ->toArray();

        $user = Auth::user();
        foreach ($lists as $key => $list) {
            $project = $user->monitoringProjects()->where('url', parse_url($list['url'])['host'])->first();
            if ($project) {
                $lists[$key]['statistics'] = MonitoringDataTableColumnsProject::find($project->id);
            }
        }

        $paginate = (int)ceil($sql->count() / $request->countOnPage);

        return response()->json([
            'lists' => $this->confirmArray($lists),
            'paginate' => $paginate
        ]);
    }

    public function getAllChecklists(Request $request)
    {
        return CheckLists::where('user_id', Auth::id())->get()->toArray();
    }

    public function inArchive(CheckLists $project)
    {
        if (!User::isUserAdmin() && $project->user_id != Auth::id()) {
            return response()->json([
                'errors' => ['abort' => 'У вас нет прав']
            ], 422);
        }

        $project->update(['archive' => 1]);

        return 'Проект перемещён в архив';
    }

    public function archive(): array
    {
        $lists = CheckLists::where('user_id', Auth::id())
            ->with('tasks:project_id,status,active_after')
            ->with('labels')
            ->where('archive', 1)
            ->get(['icon', 'url', 'id'])
            ->toArray();

        return $this->confirmArray($lists);
    }

    public function destroy(CheckLists $project)
    {
        if (!User::isUserAdmin() && $project->user_id != Auth::id()) {
            return response()->json([
                'errors' => ['abort' => 'У вас нет прав']
            ], 422);
        }

        if ($project->archive) {
            $project->delete();

            ChecklistStubs::where('checklist_id', $project->id)
                ->update([
                    'checklist_id' => null
                ]);
        }

        return 'Чеклист был удалён';
    }

    public function restore(CheckLists $project)
    {
        if (!User::isUserAdmin() && $project->user_id != Auth::id()) {
            return response()->json([
                'errors' => ['abort' => 'У вас нет прав']
            ], 422);
        }

        if ($project->archive) {
            $project->update(['archive' => 0]);
        }

        return 'Чеклист был восстановлен';
    }

    public function createLabel(Request $request)
    {
        if (CheckListsLabels::where('user_id', Auth::id())->where('name', $request->name)->count() > 0) {
            return response()->json([
                'errors' => ['unique' => 'У вас уже существует метка с таким названием']
            ], 422);
        }

        $label = CheckListsLabels::create([
            'user_id' => Auth::id(),
            'color' => $request->color,
            'name' => $request->name,
        ]);

        return [
            'message' => 'Новая метка успешно создана',
            'label' => $label
        ];
    }

    public function removeLabel(CheckListsLabels $label)
    {
        if ($label->user_id !== Auth::id() && !User::isUserAdmin()) {
            return response()->json([
                'errors' => ['abort' => 'У вас нет прав']
            ], 422);
        }

        $label->delete();

        return 'Метка успешно удалена';
    }

    public function editLabel(Request $request): string
    {
        $sql = CheckListsLabels::where('id', $request->id);

        if (!User::isUserAdmin()) {
            $sql = $sql->where('user_id', Auth::id());
        }

        $sql->update([
            $request->type => $request->target
        ]);

        return 'Метка успешно изменена';
    }

    public function createRelation(Request $request)
    {
        if (empty($request->checklistId) || empty($request->labelId)) {
            return response()->json([
                'errors' => ['unique' => 'Вы должны выбрать чеклист и метку']
            ], 422);
        }

        $link = CheckListProjectLabels::where('checklist_project_id', $request->checklistId)
            ->where('checklist_label_id', $request->labelId)
            ->first();

        if ($link === null) {
            CheckListProjectLabels::create([
                'checklist_project_id' => $request->checklistId,
                'checklist_label_id' => $request->labelId,
            ]);

            return CheckListsLabels::find($request->labelId);
        } else {
            return response()->json([
                'errors' => ['unique' => 'Метка уже привязана к проекту']
            ], 422);
        }
    }

    public function removeRelation(Request $request): string
    {
        CheckListProjectLabels::where('checklist_project_id', $request->checkListID)
            ->where('checklist_label_id', $request->labelID)
            ->delete();

        return 'Метка успешно удалена';
    }

    public function getTasks(Request $request): array
    {
        $sql = ChecklistTasks::where('project_id', $request->input('id'));

        if ($request->sort === 'deactivated') {
            $sql->whereDate('active_after', '<=', Carbon::now());
        }

        if (isset($request->search)) {
            $sql->where('name', 'like', "%$request->search%");
        }

        if ($request->sort === 'new-sort') {
            $sql->orderByDesc('id');
        } elseif ($request->sort === 'old-sort') {
            $sql->orderBy('id');
        } elseif ($request->sort != 'all') {
            $sql->where('status', $request->sort);
        }

        $tasks = $sql->get()->toArray();

        if (empty($request->search)) {
            $tasks = $this->buildTaskStructure($tasks);
        }

        $paginate = (int)ceil($sql->where('subtask', 0)->count() / $request->count);

        return [
            'checklist' => $this->confirmArray([
                CheckLists::where('id', $request->id)->where('user_id', Auth::id())->with('tasks')->first()
            ]),
            'tasks' => array_slice($tasks, $request->input('skip', 0), $request->input('count', 3)),
            'paginate' => $paginate
        ];
    }

    public function removeRepeatTask(Request $request): JsonResponse
    {
        ChecklistTasks::where('status', 'repeat')
            ->where('id', $request->id)
            ->delete();

        return response()->json([], 200);
    }

    public function removeTask(Request $request): string
    {
        $task = ChecklistTasks::where('id', $request->input('id'))->first();
        $id = $task->project_id;
        $task->delete();

        if (filter_var($request->removeSubTasks, FILTER_VALIDATE_BOOLEAN)) {
            $childIds = [];
            $this->findChildIds($request->input('id'), $childIds);

            ChecklistNotification::whereIn('checklist_task_id', $childIds)->delete();
            ChecklistTasks::whereIn('id', $childIds)->delete();
        } else {
            ChecklistTasks::where('subtask', 1)
                ->where('task_id', $request->input('id'))
                ->update([
                    'subtask' => 0,
                    'task_id' => null
                ]);
        }

        $tasks = ChecklistTasks::where('project_id', $id)
            ->whereDate('active_after', '<=', Carbon::now())
            ->get()
            ->toArray();

        $tree = $this->buildTaskStructure($tasks);

        ChecklistStubs::where('checklist_id', $id)
            ->update([
                'tree' => json_encode($tree)
            ]);

        return 'Успешно удалено';
    }

    public function findChildIds($parentId, &$childIds)
    {
        $children = ChecklistTasks::where('task_id', $parentId)
            ->whereDate('active_after', '<=', Carbon::now())
            ->pluck('id')
            ->toArray();

        foreach ($children as $child) {
            $childIds[] = $child;
            $this->findChildIds($child, $childIds);
        }
    }

    public function editTask(Request $request): JsonResponse
    {
        if (empty($request->value)) {
            return response()->json([
                'errors' => ['not empty' => 'Значение не может быть пустым']
            ], 422);
        }

        if ($request->type === 'deadline') {
            $date = Carbon::parse($request->value);
            $updates = [
                $request->type => $request->value,
            ];

            if ($date->isPast()) {
                $updates['status'] = 'expired';
            }

            ChecklistTasks::where('id', $request->id)->update($updates);

            $notification = ChecklistNotification::where('checklist_task_id', $request->id)->first();

            if (isset($notification)) {
                $notification->update([
                    'deadline' => $request->value
                ]);
            } else {
                ChecklistNotification::create([
                    'checklist_task_id' => $request->id,
                    'user_id' => Auth::id(),
                    'deadline' => $request->value
                ]);
            }

            ChecklistNotification::updateOrCreate(
                ['checklist_task_id' => $request->id],
                ['deadline' => $request->value],
            );

            return response()->json([
                'newStatus' => $updates['status'] ?? 'undefined'
            ]);

        } else {
            ChecklistTasks::where('id', $request->id)
                ->update([
                    $request->type => $request->value
                ]);
        }

        if ($request->type === 'status' && $request->value === 'ready') {
            ChecklistTasks::where('id', $request->id)
                ->update([
                    'end_date' => Carbon::now()
                ]);
        }

        return response()->json();
    }

    public function editRepeatTask(Request $request)
    {
        ChecklistTasks::where('id', $request->id)
            ->where('status', 'repeat')
            ->update([
                $request->name => $request->value
            ]);
    }

    public function addNewTasks(Request $request): string
    {
        $this->createSubTasks($request->input('tasks'), $request->input('id'), $request->input('parentID'));

        $tasks = ChecklistTasks::where('project_id', $request->input('id'))
            ->whereDate('active_after', '<=', Carbon::now())
            ->get()
            ->toArray();

        $tree = $this->buildTaskStructure($tasks);

        ChecklistStubs::where('checklist_id', $request->input('id'))
            ->update([
                'tree' => json_encode($tree)
            ]);

        return 'Успешно';
    }

    public function getStubs()
    {
        return ChecklistStubs::where('user_id', Auth::id())
            ->orWhere('type', 'classic')
            ->orderByDesc('type')
            ->get();
    }

    public function getClassicStubs(Request $request): JsonResponse
    {
        $sql = ChecklistStubs::where('type', 'classic');

        if ($request->input('name')) {
            $sql->where('name', 'like', "%$request->name%");
        }

        $stubs = $sql->skip($request->input('skip', 0))
            ->take($request->input('count', 3))
            ->get();

        $paginate = (int)ceil($sql->count() / $request->input('count', 3));

        return response()->json([
            'stubs' => $stubs,
            'paginate' => $paginate
        ]);
    }

    public function getPersonalStubs(Request $request)
    {
        $sql = ChecklistStubs::where('type', 'personal')
            ->where('user_id', Auth::id());

        if ($request->input('name')) {
            $sql->where('name', 'like', "%$request->name%");
        }

        $stubs = $sql->skip($request->input('skip', 0))
            ->take($request->input('count', 3))
            ->get();

        $paginate = (int)ceil($sql->count() / $request->input('count', 3));

        return response()->json([
            'stubs' => $stubs,
            'paginate' => $paginate
        ]);
    }

    public function removeStub(ChecklistStubs $stub): string
    {
        if ($stub->classic && User::isUserAdmin()) {
            $stub->delete();
        } else if ($stub->user_id === Auth::id()) {
            $stub->delete();
        }

        return 'Успешно';
    }

    public function relevanceProjects()
    {
        $projects = ProjectRelevanceHistory::where('user_id', Auth::id())->get()->pluck('name');

        foreach ($projects as $key => $project) {
            if (CheckLists::where('user_id', Auth::id())->where('url', "https://$project")->count() > 0) {
                unset($projects[$key]);
            }
        }

        return $projects;
    }

    public function metaTagsProjects()
    {
        $projects = MetaTag::where('user_id', Auth::id())->get()->pluck('links');

        foreach ($projects as $key => $project) {
            if (CheckLists::where('user_id', Auth::id())->where('url', "https://$project")->count() > 0) {
                unset($projects[$key]);
            }
        }

        return $projects;
    }

    public function monitoringProjects()
    {
        $projects = Auth::user()->monitoringProjects->pluck('url');

        foreach ($projects as $key => $project) {
            if (CheckLists::where('user_id', Auth::id())->where('url', "https://$project")->count() > 0) {
                unset($projects[$key]);
            }
        }

        return $projects;
    }

    public function monitoringSites()
    {
        $projects = DomainMonitoring::where('user_id', Auth::id())->get()->pluck('project_name');

        foreach ($projects as $key => $project) {
            if (CheckLists::where('user_id', Auth::id())->where('url', "https://$project")->count() > 0) {
                unset($projects[$key]);
            }
        }

        return $projects;
    }

    public function multiplyCreate(Request $request): JsonResponse
    {
        $fails = [];
        foreach ($request->urls as $url) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $fullUrl = "https://" . $url;
            } else {
                $fullUrl = $url;
            }

            try {
                $client = new Client();
                $response = $client->get($fullUrl);
                if ($response->getStatusCode() === 200) {
                    $icon = $this->findIcon($response->getBody()->getContents());
                    CheckLists::create([
                        'user_id' => Auth::id(),
                        'icon' => $this->saveIcon($icon, $fullUrl),
                        'url' => $fullUrl,
                    ]);
                }
            } catch (Throwable $exception) {
                $fails[] = "<u><a href='$fullUrl' target='_blank'>$fullUrl</a></u> не удалось подключится, проект не был сохранён";
            }
        }

        return response()->json([
            'message' => 'Новые проекты успешно добавлены',
            'fails' => $fails
        ]);
    }

    public function getNotifications()
    {
        return ChecklistNotification::where('status', '!=', 'wait')
            ->where('user_id', Auth::id())
            ->orderBy('status')
            ->with('task.project')
            ->get();
    }

    public function readNotification(ChecklistNotification $notification): JsonResponse
    {
        $notification->update([
            'status' => 'read'
        ]);

        return response()->json([]);
    }

    public function deleteNotification(ChecklistNotification $notification): JsonResponse
    {
        $notification->delete();

        return response()->json();
    }

    private function createSubTasks($tasks, $projectId, $taskId = null)
    {
        foreach ($tasks as $task) {
            $task = $task[0] ?? $task;
            $deadline = isset($task['deadline']) ? Carbon::parse($task['deadline'])->toDateTimeString() : Carbon::now()->toDateTimeString();

            $object = [
                'project_id' => $projectId,
                'name' => $task['name'] ?? 'Без названия',
                'status' => $task['status'],
                'description' => $task['description'] ?? '',
                'date_start' => isset($task['start']) ? Carbon::parse($task['start'])->toDateTimeString() : Carbon::now()->toDateTimeString(),
                'deadline' => $deadline,
            ];

            if ($task['status'] === 'deactivated') {
                $object['active_after'] = $task['active_after'];
                $object['date_start'] = $task['active_after'];
                $object['deadline'] = Carbon::parse($task['active_after'])->addDays($task['count_days']);
            } else if ($task['status'] === 'repeat') {
                $object['weekends'] = $task['weekends'];

                if ($task['weekends']) {
                    $object['date_start'] = Carbon::parse($task['active_after'])->addWeekdays($task['repeat_after']);
                } else {
                    $object['date_start'] = Carbon::parse($task['active_after'])->addDays($task['repeat_after']);
                }

                $object['deadline_every'] = $task['count_days'];
                $object['repeat_every'] = $task['repeat_after'];
            }

            if (isset($taskId)) {
                $object['subtask'] = 1;
                $object['task_id'] = $taskId;
            }

            $newRecord = ChecklistTasks::create($object);

            ChecklistNotification::create([
                'checklist_task_id' => $newRecord->id,
                'user_id' => Auth::id(),
                'deadline' => $deadline
            ]);

            if (isset($task['subtasks'])) {
                $this->createSubTasks($task['subtasks'], $projectId, $newRecord->id);
            }
        }
    }

    private function findIcon($html)
    {
        $document = new HtmlDocument();
        $document->load(mb_strtolower($html));

        $elem = $document->find('link[rel="shortcut icon"]');

        if ($elem === []) {
            $elem = $document->find('link[rel="icon"]');
        }

        return $elem;
    }

    private function saveIcon($icon, $fullUrl): ?string
    {
        $md5 = md5(microtime(true));
        $path = "/checklist/$md5.jpg";

        if (count($icon) > 0 && $icon[0]->attr['href']) {
            if (filter_var($icon[0]->attr['href'], FILTER_VALIDATE_URL)) {
                $faviconData = file_get_contents($icon[0]->attr['href']);
            } else if (filter_var("https://" . parse_url($fullUrl)['host'] . $icon[0]->attr['href'], FILTER_VALIDATE_URL)) {
                $faviconData = file_get_contents("https://" . parse_url($fullUrl)['host'] . $icon[0]->attr['href']);
            } else {
                $faviconData = 'no data';
            }

            Storage::put($path, $faviconData);
        }

        return $path;
    }

    private function confirmArray($lists): array
    {
        foreach ($lists as $key => $list) {
            $deactivated = 0;
            $expired = 0;
            $repeat = 0;
            $inWork = 0;
            $ready = 0;
            $new = 0;

            foreach ($list['tasks'] as $task) {
                if ($task['status'] === 'in_work') {
                    $inWork++;
                } else if ($task['status'] === 'ready') {
                    $ready++;
                } else if ($task['status'] === 'new') {
                    $new++;
                } else if ($task['status'] === 'deactivated') {
                    $deactivated++;
                } else if ($task['status'] === 'expired') {
                    $expired++;
                } else if ($task['status'] === 'repeat') {
                    $repeat++;
                }
            }

            $lists[$key]['inactive'] = $deactivated;
            $lists[$key]['expired'] = $expired;
            $lists[$key]['repeat'] = $repeat;
            $lists[$key]['ready'] = $ready;
            $lists[$key]['work'] = $inWork;
            $lists[$key]['new'] = $new;
        }

        return $lists;
    }

    private function buildTaskStructure($tasks, $parentId = null): array
    {
        $result = [];

        foreach ($tasks as $item) {
            if ($item['task_id'] === $parentId) {
                $task = [
                    'id' => $item['id'],
                    'project_id' => $item['project_id'],
                    'name' => $item['name'],
                    'status' => $item['status'],
                    'description' => $item['description'],
                    'subtask' => $item['subtask'],
                    'weekends' => $item['weekends'],
                    'task_id' => $item['task_id'],
                    'repeat_every' => $item['repeat_every'],
                    'deadline_every' => $item['deadline_every'],
                    'date_start' => $item['date_start'],
                    'deadline' => $item['deadline'],
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at']
                ];

                $subtasks = $this->buildTaskStructure($tasks, $item['id']);
                if (!empty($subtasks)) {
                    $task['subtasks'] = $subtasks;
                }

                $result[] = $task;
            }

        }

        return $result;
    }

    private function configureStubs($id)
    {
        $tasks = ChecklistTasks::where('project_id', $id)
            ->whereDate('active_after', '<=', Carbon::now())
            ->get()
            ->toArray();

        return json_encode($this->buildTaskStructure($tasks));
    }

    public function getRepeatTasks(Request $request)
    {
        $columnIndex = $request->input('order.0.column');
        $columnSortOrder = $request->input('order.0.dir');
        $columnName = $request['columns'][$columnIndex]['name'];

        $id = CheckLists::where('user_id', Auth::id())->pluck('id');

        $totalRecords = ChecklistTasks::whereIn('project_id', $id)
            ->where('status', 'repeat')
            ->count();

        $records = ChecklistTasks::whereIn('project_id', $id)
            ->orderBy($columnName, $columnSortOrder)
            ->where('status', 'repeat')
            ->with('project');

        foreach ($request['columns'] as $column) {
            $search = $column['search']['value'];
            if (isset($search)) {
                $columnSearch = $column['name'];

                switch ($columnSearch) {
                    case 'name':
                    case 'description':
                    case 'date_start':
                    case 'deadline_every':
                    case 'repeat_every':
                    case 'weekends':
                        $records->where($columnSearch, 'like', "%$search%");
                        break;
                    default:
                        break;
                }
            }
        }

        $start = $request->input('start');
        $pageNumber = floor($start / $request->input('length')) + 1;
        $records = $records->paginate($request->input('length'), ['*'], 'page', $pageNumber);

        $aaData = [];
        foreach ($records as $record) {
            $aaData[] = [
                'id' => $record->id,
                'name' => $record->name,
                'description' => $record->description,
                'date_start' => $record->date_start,
                'deadline_every' => $record->deadline_every,
                'repeat_every' => $record->repeat_every,
                'weekends' => $record->weekends,
                'project' => $record->project,
            ];
        }

        return json_encode([
            'draw' => (int)$request['draw'],
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecords,
            'aaData' => $aaData
        ]);
    }

    public function storeRepeatTasks(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'date_start' => 'required',
            'repeat_every' => 'required',
            'deadline_every' => 'required',
            'ids' => 'array|required',
        ], [
            'name.required' => 'Название задачи не может быть пустым',
            'date_start.required' => 'Вы забыли указать дату первого запуска',
            'repeat_every.required' => 'Вы забыли указать промежуток повторения задачи',
            'deadline_every.required' => 'Вы забыли указать количество дней на выполнение',
            'ids.required' => 'Вам нужно указать список ваших чеклистов',
        ]);

        try {
            DB::beginTransaction();

            $insert = [];

            foreach ($request->ids as $id) {
                $insert[] = [
                    'project_id' => $id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'repeat_every' => $request->repeat_every,
                    'deadline_every' => $request->deadline_every,
                    'weekends' => $request->weekends,
                    'date_start' => $request->date_start,
                    'status' => 'repeat',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }

            ChecklistTasks::insert($insert);
            DB::commit();

            return response()->json([
                'message' => __('Success')
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
