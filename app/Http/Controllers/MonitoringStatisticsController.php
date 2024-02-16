<?php

namespace App\Http\Controllers;

use App\Classes\Monitoring\Widgets\WidgetsAbstract;
use App\Classes\Monitoring\Widgets\WidgetsFactory;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MonitoringStatisticsController extends Controller
{
    protected $widgets;
    protected $user;
    protected $projects;
    protected $period = 6;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->projects = $this->user->monitoringProjectsWithDataTable()->get();

            return $next($request);
        });

        $this->widgets = new WidgetsFactory();
    }

    public function getPeriod(): int
    {
        return $this->period;
    }

    public function setPeriod(int $period): MonitoringStatisticsController
    {
        $this->period = $period;
        return $this;
    }

    public function index()
    {
        $menu = $this->widgets->getMenu();
        $widgets = $this->widgets->getCollection()->where('active', true)->sortBy('sort');
        $chartData = $this->chartData();
        $period = array_reverse($this->periodOfMonth($this->getPeriod())->toArray());

        return view('monitoring.statistics.index', compact('widgets', 'menu', 'chartData', 'period'));
    }

    public function activeWidgets(Request $request)
    {
        $fields = $request->input('fields', []);

        foreach ($fields as $field){
            /** @var WidgetsAbstract $widget */
            $widget = $this->widgets->getWidgetByCode($field['name']);
            $widget->activation($field['active']);
        }
    }

    public function sortWidgets(Request $request)
    {
        /** @var User $user */
        $user = $this->user;
        $ids = $request->input('ids', []);

        foreach ($ids as $sort => $id)
        {
            $sort += 1;
            $widget = $user->monitoringWidgets()->find($id);
            $widget->sort = $sort;
            $widget->save();
        }
    }

    public function managerTable(Request $request)
    {
        $users = $this->getUsersWithProjectsByStatus(MonitoringProjectUserStatusController::STATUS_PM);
        $data = collect([]);

        foreach($users as $key => $user)
        {
            $projects = $this->projects->whereIn('id', $user['projects']);

            $collect = collect([
                'name' => $user['name'],
                'count' => $projects->count(),
                'top10' => $projects->pluck('top10')->sum(),
                'top30' => $projects->pluck('top30')->sum(),
                'top100' => $projects->pluck('top100')->sum(),
                'budget' => $projects->pluck('budget')->sum(),
            ]);

            $data->push($collect);
        }

        $sorted = $this->sortDataTable($data, $request->input('columns'), $request->input('order'));

        return collect([
            'draw' => $request->input('draw'),
            'recordsTotal' => $data->count(),
            'recordsFiltered' => $data->count(),
            'data' => $sorted->values(),
        ]);
    }

    public function seoTable(Request $request)
    {
        $users = $this->getUsersWithProjectsByStatus(MonitoringProjectUserStatusController::STATUS_SEO);
        $data = collect([]);

        foreach($users as $key => $user)
        {
            $projects = $this->projects->whereIn('id', $user['projects']);

            $collect = collect([
                'id' => $user['id'],
                'name' => $user['name'],
                'count' => $projects->count(),
                'top10' => $projects->pluck('top10')->sum(),
                'top30' => $projects->pluck('top30')->sum(),
                'top100' => $projects->pluck('top100')->sum(),
                'budget' => $projects->pluck('budget')->sum(),
                'mastered' => $projects->pluck('mastered')->sum(),
            ]);

            $data->push($collect);
        }

        $columns = $request->input('columns');
        $order = $request->input('order');

        $sorted = $data->sortBy($columns[$order[0]['column']]['data'], SORT_REGULAR, $order[0]['dir'] == 'asc' ? false : true);

        return collect([
            'draw' => $request->input('draw'),
            'recordsTotal' => $data->count(),
            'recordsFiltered' => $data->count(),
            'data' => $sorted->values(),
        ]);
    }

    public function projectTable($id)
    {
        $data = collect([]);

        $users = $this->getUsersWithProjectsByStatus(MonitoringProjectUserStatusController::STATUS_SEO);

        $user = $users[$id];

        $projects = $this->projects->whereIn('id', $user['projects']);

        $colMonth = 6;
        $periods = array_reverse($this->periodOfMonth($colMonth - 1)->toArray());

        foreach($projects as $project)
        {
            $collect = collect([
                'name' => $project['url'],
                'data' => collect([
                    collect([
                        'month' => Carbon::now()->monthName,
                        'top10' => $project['top10'],
                        'mastered' => $project['mastered'],
                        'words' => $project['words']
                    ]),
                ]),
            ]);

            foreach ($periods as $date)
            {
                $statistics = collect([
                    'month' => $date->monthName,
                    'top10' => 0,
                    'mastered' => 0,
                    'words' => 0,
                ]);

                $statProject = $this->getStatisticsProjectLastOfMonth(User::find($user['id']), $date)->where('id', $project['id'])->first();

                if(!is_null($statProject))
                {
                    $statistics['top10'] = $statProject['top10'];
                    $statistics['mastered'] = $statProject['mastered'];
                    $statistics['words'] = $statProject['words'];
                }

                $collect['data']->push($statistics);
            }

            $data->push($collect);
        }

        return view('monitoring.statistics.projects', compact('data', 'periods'));
    }

    public function attentionTable(Request $request)
    {
        $date = $request->input('date');

        $data = collect([]);

        /** @var User $user */
        $user = $this->user;
        $projects = $this->getStatisticsProjectLastOfMonth($user, Carbon::create($date));

        foreach($projects as $project)
        {
            $nameUsers = [];
            foreach($project['users'] as $user)
                $nameUsers[] = implode(' ', [$user['name'], $user['last_name']]);

            $collect = collect([
                'name' => $project['url'],
                'users' => implode(', ', $nameUsers),
                'top10' => $project['top10'],
                'mastered' => $project['mastered'],
                'words' => $project['words'],
            ]);

            $data->push($collect);
        }

        return collect([
            'data' => $data,
        ]);
    }

    private function sortDataTable(Collection $data, array $columns, array $order): Collection
    {
        return $data->sortBy($columns[$order[0]['column']]['data'], SORT_REGULAR, $order[0]['dir'] == 'asc' ? false : true);
    }

    protected function chartData()
    {
        $chartData = ['dates' => collect([]), 'budget' => collect([]), 'mastered' => collect([])];

        /** @var User $user */
        $user = $this->user;

        $period = $this->periodOfMonth(6);

        foreach ($period as $date)
        {
            $projects = $this->getStatisticsProjectLastOfMonth($user, $date);

            $budget = $projects->pluck('budget')->sum();
            $mastered = $projects->pluck('mastered')->sum();

            $chartData['dates']->push($date->monthName);
            $chartData['budget']->push($budget);
            $chartData['mastered']->push($mastered);
        }

        return $chartData;
    }

    private function getStatisticsProjectLastOfMonth(User $user, Carbon $date)
    {
        $statistics = $user->statistics()
            ->whereDate('created_at', $date->lastOfMonth())
            ->get();

        $projects = $statistics->pluck('monitoring_project')->unique('id');

        return $projects;
    }

    private function periodOfMonth(int $month): CarbonPeriod
    {
        $carbon = Carbon::now()->subMonth($month)->monthsUntil(Carbon::now()->subMonth(1));

        return $carbon;
    }

    private function getUsers(): Collection
    {
        return $this->projects->pluck('users')->flatten();
    }

    private function getUsersWithProjectsByStatus(string $code): array
    {
        $filtered = $this->filterUsersByStatus($code);

        $users = [];
        foreach($filtered as $user)
        {
            $users[$user['id']]['id'] = $user['id'];
            $users[$user['id']]['name'] = $user['fullName'];
            $users[$user['id']]['projects'][] = $user['pivot']['monitoring_project_id'];
        }

        return $users;
    }

    private function filterUsersByStatus(string $code): Collection
    {
        $users = $this->getUsers();
        $status = MonitoringProjectUserStatusController::getIdStatusByCode($code);

        $filtered = $users->filter(function($val) use ($status){
            return $val['pivot']['status'] === $status;
        });

        return $filtered;
    }

}
