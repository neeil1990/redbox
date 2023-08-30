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
use Illuminate\Support\Carbon;
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

        }

        return view('main-projects.statistics-modules', [
            'projects' => $projects,
        ]);
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
                } else if ($column['name'] === 'roles') {
                    if ($search !== 'Любой') {
                        $usersIds->whereHas('roles', function ($query) use ($search) {
                            $query->where('name', $search);
                        })->with('roles');
                    } else {
                        $usersIds->with('roles');
                    }
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

    public function actionsHistory(Request $request): array
    {
        $usersIds = User::where('statistic', 1)->pluck('id')->toArray();
        $date = explode(' - ', $request->dateRange);

        $statistics = VisitStatistic::where('project_id', $request->projectId)
            ->whereIn('user_id', $usersIds)
            ->whereBetween('date', [
                date('Y-m-d', strtotime($date[0])),
                date('Y-m-d', strtotime($date[1]))
            ])
            ->with('user')
            ->get(['date', 'user_id', 'actions_counter', 'refresh_page_counter', 'seconds'])
            ->groupBy('date');

        $days = [];
        $seconds = [];
        $refresh = [];
        $actions = [];

        foreach ($statistics as $day => $statistic) {

            $secondsInDay = 0;
            $refreshInDay = 0;
            $actionsInDay = 0;

            foreach ($statistic as $item) {
                $secondsInDay += $item->seconds;
                $refreshInDay += $item->refresh_page_counter;
                $actionsInDay += $item->actions_counter;
            }

            $days[] = $day;
            $seconds[] = $secondsInDay;
            $refresh[] = $refreshInDay;
            $actions[] = $actionsInDay;
        }

        return [
            'days' => $days,
            'refresh' => $refresh,
            'seconds' => $seconds,
            'actions' => $actions,
        ];
    }

    public function getDateRangeModuleStatistics(MainProject $project): JsonResponse
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        $usersIds = User::where('statistic', 1)->pluck('id')->toArray();

        return response()->json([
            'dates' => VisitStatistic::where('project_id', $project->id)
                ->whereIn('user_id', $usersIds)
                ->groupBy('date')
                ->get('date')
                ->toArray()
        ]);
    }

    public function statisticsModules(Request $request): array
    {
        return $this->getLineBarStat($request->action, explode(' - ', $request->dateRange));
    }

    private function getLineBarStat(string $index, array $dateRange): array
    {
        $projects = MainProject::with('statistics')
            ->whereHas('statistics', function ($query) use ($dateRange) {
                $query->whereBetween('date', [
                    date('Y-m-d', strtotime($dateRange[0])),
                    date('Y-m-d', strtotime($dateRange[1]))
                ]);
            })->get();

        $dates = [];

        $startDate = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        foreach ($projects as $key => $project) {
            $newStat = [];
            foreach ($project['statistics'] as $statistic) {
                $checkDate = Carbon::parse($statistic['date']);
                if ($checkDate->isBefore($startDate) || $checkDate->isAfter($endDate)) {
                    continue;
                }

                $newStat[$statistic['date']] = $statistic;
                if (in_array($statistic['date'], $dates)) {
                    continue;
                } else {
                    $dates[] = $statistic['date'];
                }
            }

            $projects[$key]['newStat'] = $newStat;
        }

        $datasets = [];
        foreach ($projects as $project) {
            $stat = array_keys($project['newStat']);
            $data = [];
            foreach ($dates as $date) {
                if (in_array($date, $stat)) {
                    $data[] = $project['newStat'][$date][$index];
                } else {
                    $data[] = 0;
                }
            }
            $datasets[] = [
                'label' => __($project['title']),
                'backgroundColor' => $project['color'],
                'data' => $data
            ];
        }

        return [
            'dates' => $dates,
            'datasets' => $datasets
        ];
    }
}
