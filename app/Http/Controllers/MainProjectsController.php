<?php

namespace App\Http\Controllers;

use App\Common;
use App\MainProject;
use App\User;
use App\VisitStatistic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $request = $request->all();
        $request['show'] = $request['show'] === 'on';

        MainProject::create($request);
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
        $usersIds = User::where('statistic', 1)->with('roles')->get(['id'])->pluck('id')->toArray();

        $statistics = VisitStatistic::where('project_id', $project->id)
            ->whereIn('user_id', $usersIds)
            ->with('user')
            ->get(['date', 'user_id', 'actions_counter', 'refresh_page_counter', 'seconds'])
            ->groupBy('date')
            ->toArray();

        $result = [];
        foreach ($statistics as $date => $info) {
            $result[$date]['actionsCounter'] = 0;
            $result[$date]['refreshPageCounter'] = 0;
            $result[$date]['time'] = 0;
            $users = [];

            foreach ($info as $elem) {
                $result[$date]['actionsCounter'] += $elem['actions_counter'];
                $result[$date]['refreshPageCounter'] += $elem['refresh_page_counter'];
                $result[$date]['time'] += $elem['seconds'];

                $elem['user']['actionsCounter'] = $elem['actions_counter'];
                $elem['user']['refreshPageCounter'] = $elem['refresh_page_counter'];
                $elem['user']['time'] = Common::secondsToDate($elem['seconds']);

                $users[] = $elem['user'];
            }

            $result[$date]['users'] = $users;
        }

        foreach ($result as $date => $info) {
            $result[$date]['time'] = Common::secondsToDate($info['time']);
        }

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

}
