<?php

namespace App\Http\Controllers;

use App\Classes\Tariffs\Facades\Tariffs;
use App\DomainMonitoring;
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

class DomainMonitoringController extends Controller
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

        return view('domain-monitoring.index', compact('projects', 'countProjects'));
    }

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function createView()
    {
        return view('domain-monitoring.create');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        // Проверка тарифа
        if($tariff = $user->tariff())
            $tariff = $tariff->getAsArray();

        $count = DomainMonitoring::where('user_id', '=', Auth::id())->count();
        if(isset($tariff['settings']['domainMonitoringProject']) && $tariff['settings']['domainMonitoringProject'] > 0){
            if($count >= $tariff['settings']['domainMonitoringProject']){

                //abort(403, 'Для тарифа: ' . $tariff['name'] . ' лимит ' . $tariff['settings']['domainMonitoringProject'] . ' проект.');

                flash()->overlay('Для тарифа: ' . $tariff['name'] . ' лимит ' . $tariff['settings']['domainMonitoringProject'] . ' проект.', ' ')->error();
                return Redirect::route('domain.monitoring');
            }
        }

        $userId = Auth::id();
        $monitoring = new DomainMonitoring($request->all());
        $monitoring->user_id = $userId;
        $monitoring->save();

        flash()->overlay(__('Monitoring was successfully created'), ' ')->success();
        return Redirect::route('domain.monitoring');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function remove($id): RedirectResponse
    {
        DomainMonitoring::destroy($id);
        flash()->overlay(__('Monitoring was successfully deleted'), ' ')->success();

        return Redirect::route('domain.monitoring');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function checkLink($id): RedirectResponse
    {
        try {
            $project = DomainMonitoring::findOrFail($id);
            DomainMonitoring::httpCheck($project);

        } catch (Exception $exception) {
            flash()->overlay(__('Error'), ' ')->success();
        }

        return Redirect::back();
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
