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

                if ($request->input('saveStub', false) !== 'no') {
                    $data = [
                        'user_id' => Auth::id(),
                        'tree' => json_encode($request->input('tasks')),
                        'type' => $request->input('saveStub', false),
                    ];

                    // 1) довить добавление подзадачь у списка задач
                    // 2) добавление задач по времени
                    // 2.1) добавить возможность выбирать не дату начала \ окончания, а диапозон (+)
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
        }

        return 'Успешно';
    }


    public function update(Request $request)
    {
        $this->createSubTasks($request->input('tasks'), $request->input('id'));

        $tasks = ChecklistTasks::where('project_id', $request->input('id'))->get()->toArray();
        $tree = $this->buildTaskStructure($tasks);

        ChecklistStubs::where('checklist_id', $request->input('id'))
            ->update([
                'tree' => json_encode($tree)
            ]);

        return 'Успешно';
    }

    public function storeStubs(Request $request): string
    {
        if ($request->input('action') === 'all') {
            ChecklistStubs::create([
                'user_id' => Auth::id(),
                'tree' => json_encode($request->input('stubs')),
                'type' => 'classic',
            ]);

            ChecklistStubs::create([
                'user_id' => Auth::id(),
                'tree' => json_encode($request->input('stubs')),
                'type' => 'personal',
            ]);
        } else {
            ChecklistStubs::create([
                'user_id' => Auth::id(),
                'tree' => json_encode($request->input('stubs')),
                'type' => $request->input('action'),
            ]);
        }

        return 'Успешно';
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
            ->with('tasks:project_id,status')
            ->with('labels')
            ->get(['icon', 'url', 'id'])
            ->toArray();

        $paginate = (int)ceil($sql->count() / $request->countOnPage);

        return response()->json([
            'lists' => $this->confirmArray($lists),
            'paginate' => $paginate
        ]);
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
            ->with('tasks:project_id,status')
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

        if (isset($request->search)) {
            $sql->where('name', 'like', "%$request->search%");
        }

        if ($request->sort === 'new') {
            $sql->orderBy('id');
        } elseif ($request->sort === 'old') {
            $sql->orderByDesc('id');
        } elseif ($request->sort != 'all') {
            $sql->where('status', $request->sort);
        }

        $tasks = $sql
            ->get()
            ->toArray();

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

        $tasks = ChecklistTasks::where('project_id', $id)->get()->toArray();
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

        return response()->json();
    }

    public function addNewTasks(Request $request): string
    {
        $this->createSubTasks($request->input('tasks'), $request->id, $request->parentID ?? null);

        return 'Успешно';
    }

    public function getStubs()
    {
        return ChecklistStubs::where('user_id', Auth::id())
            ->orWhere('type', 'classic')
            ->orderByDesc('type')
            ->get();
    }

    public function getClassicStubs()
    {
        return ChecklistStubs::where('type', 'classic')
            ->get();
    }

    public function getPersonalStubs()
    {
        return ChecklistStubs::where('type', 'personal')
            ->where('user_id', Auth::id())
            ->get();
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
        $projects = MetaTag::where('user_id', Auth::id())->get()->pluck('name');

        foreach ($projects as $key => $project) {
            if (CheckLists::where('user_id', Auth::id())->where('url', "https://$project")->count() > 0) {
                unset($projects[$key]);
            }
        }

        return $projects;
    }

    public function monitoringProjects()
    {
        $projects = Auth::user()->monitoringProjects->pluck('name');

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

    public function multiplyCreate(Request $request): string
    {
        foreach ($request->urls as $url) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $fullUrl = "https://" . $url;
            } else {
                $fullUrl = $url;
            }

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
        }

        return 'Новые проекты успешно добавлены';
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
            $date = Carbon::parse($task['deadline']);
            $deadline = isset($task['deadline']) ? Carbon::parse($task['deadline'])->toDateTimeString() : Carbon::now()->toDateTimeString();

            $object = [
                'project_id' => $projectId,
                'name' => $task['name'] ?? 'Без названия',
                'status' => $date->isPast() ? 'expired' : $task['status'],
                'description' => $task['description'] ?? '',
                'deadline' => $deadline,
                'date_start' => isset($task['start']) ? Carbon::parse($task['start'])->toDateTimeString() : Carbon::now()->toDateTimeString(),
            ];

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
            $inWork = 0;
            $ready = 0;
            $expired = 0;

            foreach ($list['tasks'] as $task) {
                if ($task['status'] === 'in_work') {
                    $inWork++;
                } else if ($task['status'] === 'ready') {
                    $ready++;
                } else {
                    $expired++;
                }
            }

            $lists[$key]['work'] = $inWork;
            $lists[$key]['ready'] = $ready;
            $lists[$key]['expired'] = $expired;
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
                    'task_id' => $item['task_id'],
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
}
