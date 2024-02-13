<?php

namespace App\Http\Controllers;

use App\Classes\Cron\UserMonitoringProjectSave;
use App\Classes\Monitoring\Widgets\WidgetsAbstract;
use App\Classes\Monitoring\Widgets\WidgetsFactory;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MonitoringStatisticsController extends Controller
{
    protected $widgets;
    protected $user;
    protected $projects;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->projects = $this->user->monitoringProjectsWithDataTable()->get();

            return $next($request);
        });

        $this->widgets = new WidgetsFactory();
    }

    public function index()
    {
        $menu = $this->widgets->getMenu();
        $widgets = $this->widgets->getCollection()->where('active', true)->sortBy('sort');
        $chartData = $this->chartData();

        return view('monitoring.statistics.index', compact('widgets', 'menu', 'chartData'));
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
        $users = $this->getPMUsers();
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

    public function seoTable()
    {
        return collect([
            'draw' => 1,
            'recordsTotal' => 3,
            'recordsFiltered' => 3,
            'data' => collect([
                collect([
                    'name' => '1',
                    'count' => '1',
                    'top10' => '1',
                    'top30' => '1',
                    'top100' => '1',
                    'budget' => '1',
                ]),
            ]),
        ]);
    }

    protected function chartData()
    {
        $chartData = ['dates' => collect([]), 'budget' => collect([]), 'mastered' => collect([])];

        /** @var User $user */
        $user = $this->user;

        $period = $this->periodOfMonth(6);

        foreach ($period as $date)
        {
            $statistics = $user->statistics()
                ->whereDay('created_at', UserMonitoringProjectSave::DAY)
                ->whereMonth('created_at', $date->month)
                ->get();

            $projects = $statistics->pluck('monitoring_project')->unique('id');

            $budget = $projects->pluck('budget')->sum();
            $mastered = $projects->pluck('mastered')->sum();

            $chartData['dates']->push($date->monthName);
            $chartData['budget']->push($budget);
            $chartData['mastered']->push($mastered);
        }

        return $chartData;
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

    private function getPMUsers(): array
    {
        $status = MonitoringProjectUserStatusController::getIdStatusByCode(MonitoringProjectUserStatusController::STATUS_PM);
        $users = $this->getUsers();

        $filtered = $users->filter(function($val) use ($status){
            return $val['pivot']['status'] === $status;
        });

        $users = [];
        foreach($filtered as $user)
        {
            $users[$user['id']]['id'] = $user['id'];
            $users[$user['id']]['name'] = $user['fullName'];
            $users[$user['id']]['projects'][] = $user['pivot']['monitoring_project_id'];
        }

        return $users;
    }

    private function getSEOUsers(): Collection
    {
        $status = MonitoringProjectUserStatusController::getIdStatusByCode(MonitoringProjectUserStatusController::STATUS_SEO);
        $users = $this->getUsers();

        return $users->filter(function($val) use ($status){
            return $val['pivot']['status'] === $status;
        });
    }

}
