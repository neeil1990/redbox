<?php

namespace App\Http\Controllers;

use App\ClickTracking;
use App\Common;
use App\MainProject;
use App\User;
use App\VisitStatistic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class MainProjectsController extends Controller
{
    protected $roles;

    public function __construct()
    {
        $this->middleware(['permission:Main projects']);

        $this->roles = Role::all()->pluck('name', 'name');
    }

    public function index()
    {
        $data = MainProject::orderBy('position', 'asc')->get();

        return view('main-projects.index', compact('data'));
    }

    public function create()
    {
        $roles = $this->roles;

        return view('main-projects.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'title' => ['required'],
            'position' => ['required', 'unique:main_projects'],
            'color' => ['required', 'unique:main_projects'],
            'link' => ['required'],
            'icon' => ['required'],
        ], [
            'position.unique' => 'Такая позиция уже существует',
        ]);

        $record = $request->all();

        $record['show'] = $record['show'] === 'on';

        $record['buttons'] = json_encode(explode("\r\n", $record['buttons']));

        MainProject::create($record);

        return redirect()->route('main-projects.index');
    }

    public function edit($id)
    {
        $data = MainProject::find($id);
        $roles = $this->roles;
        return view('main-projects.edit', compact('data', 'roles'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $project = MainProject::find($id);
        $this->validate($request, [
            'title' => ['required'],
            'position' => Rule::unique('main_projects')->ignore($project->position, 'position'),
            'color' => Rule::unique('main_projects')->ignore($project->color, 'color'),
            'link' => ['required'],
            'icon' => ['required'],
        ], [
            'position.unique' => 'Такая позиция уже существует',
        ]);

        $request = $request->all();

        $request['show'] = isset($request['show']);
        $request['buttons'] = json_encode(explode("\r\n", $request['buttons']));

        $project->update($request);

        return redirect()->route('main-projects.index');
    }

    public function destroy($id): JsonResponse
    {
        MainProject::where('id', $id)->delete();

        return response()->json([], 200);
    }

    public function statistics(MainProject $project)
    {
        $usersIds = User::where('statistic', 1)->pluck('id')->toArray();

        $statistics = VisitStatistic::where('project_id', $project->id)
            ->whereIn('user_id', $usersIds)
            ->with('user')
            ->get(['date', 'user_id', 'actions_counter', 'refresh_page_counter', 'seconds'])
            ->groupBy('date');

        $result = $statistics->map(function ($info, $date) {
            $actionsCounter = $info->sum('actions_counter');
            $refreshPageCounter = $info->sum('refresh_page_counter');
            $time = $info->sum('seconds');

            $users = $info->map(function ($elem) {
                $user = $elem['user'];
                $user['actionsCounter'] = $elem['actions_counter'];
                $user['refreshPageCounter'] = $elem['refresh_page_counter'];
                $user['time'] = Common::secondsToDate($elem['seconds']);

                return $user;
            });

            return [
                'actionsCounter' => $actionsCounter,
                'refreshPageCounter' => $refreshPageCounter,
                'time' => $time,
                'users' => $users,
            ];
        });

        $result = $result->map(function ($info) {
            $info['time'] = Common::secondsToDate($info['time']);
            return $info;
        });

        return view('main-projects.statistics', compact('result', 'project'));
    }

    public function moduleVisitStatistics()
    {
        $projects = MainProject::with('statistics')->get()->toArray();
        $names = [];
        $colors = [];
        $refreshes = [];
        $actions = [];
        $seconds = [];

        foreach ($projects as $key => $project) {
            $sumActions = 0;
            $sumRefresh = 0;
            $countSeconds = 0;

            foreach ($project['statistics'] as $statistic) {
                $sumActions += $statistic['actions_counter'];
                $sumRefresh += $statistic['refresh_page_counter'];
                $countSeconds += $statistic['seconds'];
            }

            $projects[$key]['statistics'] = [
                'actions_counter' => $sumActions,
                'refresh_page_counter' => $sumRefresh,
                'seconds' => $countSeconds,
            ];

            $names[] = __($project['title']);
            $colors[] = $project['color'];
            $actions[] = $sumActions;
            $refreshes[] = $sumRefresh;
            $seconds[] = $countSeconds;
        }

        $colors = json_encode($colors);
        $names = json_encode($names);
        $actions = json_encode($actions);
        $refreshes = json_encode($refreshes);
        $seconds = json_encode($seconds);

        return view('main-projects.statistics-modules',
            compact('projects', 'colors', 'names', 'actions', 'refreshes', 'seconds')
        );
    }

    public function actions($id, Request $request)
    {
        $usersIds = User::where('statistic', 1);
        $records = ClickTracking::where('project_id', $id);

        foreach ($request['columns'] as $column) {
            $search = $column['search']['value'];

            if (isset($search)) {
                if ($column['name'] === 'email') {
                    $usersIds->where('email', 'like', "%$search%");
                } else if ($column['name'] === 'url') {
                    $records->where('url', $search);
                } else if ($column['name'] === 'roles' && $search !== 'Любой') {
                    $usersIds->whereHas('roles', function ($query) use ($search) {
                        $query->where('name', $search);
                    })->with('roles');
                } else {
                    $records->where('button_text', $column['name'])
                        ->where('button_counter', $search);
                }
            }
        }

        $usersIds = $usersIds->take($request['length'])
            ->pluck('id');

        $columnIndex = $request['order'][0]['column'];
        $columnName = $request['columns'][$columnIndex]['name'];
        $columnSortOrder = $request['order'][0]['dir'];


        $records = $records
            ->whereIn('user_id', $usersIds)
            ->with('user')
            ->get(['user_id', 'url', 'project_id', 'button_text', 'button_counter'])
            ->groupBy('user.email');

        $data = [];
        $i = 0;
        foreach ($records as $email => $pages) {
            foreach ($pages->groupBy('url') as $actions) {
                $data[$i] = [
                    'email' => $email,
                    'roles' => $actions[0]['user']['roles']->toArray(),
                    'url' => $actions[0]['url'],
                ];
                foreach ($actions->toArray() as $page) {
                    $data[$i][str_replace(' ', '_', $page['button_text'])] = $page['button_counter'];
                }
                $i++;
            }
        }

        $collection = collect($data);

        if ($columnSortOrder === 'asc') {
            $sortedCollection = $collection->sortBy(str_replace(' ', '_', $columnName));
        } else {
            $sortedCollection = $collection->sortByDesc(str_replace(' ', '_', $columnName));
        }

        $sortedArray = $sortedCollection->values()->all();

        $filteredData = [
            'draw' => intval($request['draw']),
            'iTotalRecords' => count($records),
            'iTotalDisplayRecords' => count($records),
            'aaData' => $sortedArray
        ];

        return json_encode($filteredData);
    }
}
