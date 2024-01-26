<?php

namespace App\Http\Controllers;

use App\Classes\Monitoring\Widgets\WidgetsAbstract;
use App\Classes\Monitoring\Widgets\WidgetsFactory;
use App\User;
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

        return view('monitoring.statistics.index', compact('widgets', 'menu'));
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

}
