<?php

namespace App\Http\Controllers;

use App\Classes\Tariffs\Facades\Tariffs;
use App\DomainMonitoring;
use App\TariffSetting;
use App\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class MonitoringDomainController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:Domain monitoring']);
    }

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function index()
    {
        $projects = DomainMonitoring::where('user_id', '=', Auth::id())->get();
        $countProjects = count($projects);
        if ($countProjects === 0) {
            return $this->createView();
        }

        return view('site-monitoring.index', compact('projects', 'countProjects'));
    }

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function createView()
    {
        return view('site-monitoring.create');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        if (TariffSetting::checkDomainMonitoringLimits()) {
            flash()->overlay(__('Your limits are exhausted this month'), ' ')->error();

            return redirect()->route('site.monitoring');
        }

        $monitoring = new DomainMonitoring($request->all());
        $monitoring->user_id = Auth::id();
        $monitoring->save();

        flash()->overlay(__('Monitoring was successfully created'), ' ')->success();

        return Redirect::route('site.monitoring');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function remove($id): RedirectResponse
    {
        DomainMonitoring::destroy($id);
        flash()->overlay(__('Monitoring was successfully deleted'), ' ')->success();

        return Redirect::route('site.monitoring');
    }

    public function checkLink(Request $request): JsonResponse
    {
        $project = DomainMonitoring::findOrFail($request->projectId);
        DomainMonitoring::httpCheck($project);

        return response()->json([
            'status' => __($project->status),
            'code' => $project->code,
            'uptime' => round($project->uptime_percent, 2),
            'broken' => $project->broken,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {
        if (strlen($request->option) > 0 || $request->name === 'phrase') {
            DomainMonitoring::where('id', $request->id)->update([
                $request->name => $request->option,
            ]);
            return response()->json([]);
        }
        return response()->json([], 400);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeDomains(Request $request): JsonResponse
    {
        if (DomainMonitoring::destroy(explode(',', $request->ids))) {
            return response()->json([]);
        }
        return response()->json([], 400);
    }
}
