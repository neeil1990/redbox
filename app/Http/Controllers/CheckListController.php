<?php

namespace App\Http\Controllers;

use App\CheckListProjectLabels;
use App\CheckLists;
use App\CheckListsLabels;
use App\ChecklistTasks;
use App\Classes\SimpleHtmlDom\HtmlDocument;
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
        $checklist = $this->confirmArray([$checklist]);

        return view('checklist.tasks', compact('checklist'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|
            ',
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
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
        }

        return 'Успешно';
    }

    public function getChecklists(Request $request): JsonResponse
    {
        $sql = CheckLists::where('user_id', Auth::id())
            ->where('archive', 0);

        if (isset($request->url)) {
            $sql->where('url', 'like', "%$request->url%");
        }

        $lists = $sql->skip($request->input('skip', 0))
            ->take($request->input('countOnPage'), 3)
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

            return 'Метка успешно добавлена к проекту';
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
        $checklist = CheckLists::where('id', $request->id)->where('user_id', Auth::id())->with('tasks')->first();
        $tasks = $this->buildTaskHierarchy($checklist['tasks']->toArray(), null);

        return [
            'checklist' => $this->confirmArray([$checklist]),
            'tasks' => $tasks
        ];
    }

    public function removeTask(Request $request): string
    {
        ChecklistTasks::where('id', $request->id)->delete();

        if (filter_var($request->removeSubTasks, FILTER_VALIDATE_BOOLEAN)) {
            $childIds = [];
            $this->findChildIds($request->id, $childIds);

            DB::table('checklist_tasks')->whereIn('id', $childIds)->delete();
        } else {
            ChecklistTasks::where('subtask', 1)
                ->where('task_id', $request->id)
                ->update([
                    'subtask' => 0,
                    'task_id' => null
                ]);
        }

        return 'Успешно удалено';
    }

    public function findChildIds($parentId, &$childIds)
    {
        $children = DB::table('checklist_tasks')
            ->where('task_id', $parentId)
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

    public function update(Request $request)
    {
        ChecklistTasks::where('project_id', $request->id)->delete();

        $this->createSubTasks($request->tree, $request->id);
    }

    public function addNewTasks(Request $request): string
    {
        $this->createSubTasks($request->input('tasks'), $request->id, $request->parentID ?? null);

        return 'Успешно';
    }

    private function createSubTasks($tasks, $projectId, $taskId = null)
    {
        foreach ($tasks as $task) {
            $task = $task[0] ?? $task;
            $date = Carbon::parse($task['deadline']);

            $object = [
                'project_id' => $projectId,
                'name' => $task['name'] ?? 'Без названия',
                'status' => $date->isPast() ? 'expired' : $task['status'],
                'description' => $task['description'] ?? '',
                'deadline' => $date->toDateTimeString(),
            ];

            if (isset($taskId)) {
                $object['subtask'] = 1;
                $object['task_id'] = $taskId;
            }

            $newRecord = ChecklistTasks::create($object);

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

        if ($elem === []) {
            $elem = $document->find('link[rel="apple-touch-icon"]');
        }

        return $elem;
    }

    private function saveIcon($icon, $fullUrl): ?string
    {
        $md5 = md5(microtime(true));
        $path = "/checklist/$md5.jpg";

        if ($icon[0]->attr['href']) {
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

    private function buildTaskHierarchy($tasks, $parentId = null): array
    {
        $result = [];

        foreach ($tasks as $task) {
            if ($task['task_id'] == $parentId) {
                $subtasks = $this->buildTaskHierarchy($tasks, $task['id']);
                if (!empty($subtasks)) {
                    $task['subtasks'] = $subtasks;
                }
                $result[] = $task;
            }
        }

        return $result;
    }
}
