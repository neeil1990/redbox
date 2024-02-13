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
use Illuminate\Support\Facades\Auth;

class MonitoringStatisticsController extends Controller
{
    protected $widgets;
    protected $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

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

}
