<?php

namespace App\Http\Controllers;

use App\DomainMonitoring;
use App\TelegramBot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class DomainMonitoringController extends Controller
{
    public function index()
    {
        $projects = DomainMonitoring::where('user_id', '=', Auth::id())->get();
        if (count($projects) === 0) {
            return $this->createView();
        }

        return view('domain-monitoring.index', compact('projects'));
    }

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
        $userId = Auth::id();
        $monitoring = new DomainMonitoring($request->all());
        $monitoring->user_id = $userId;
        $monitoring->save();

        $bot = new TelegramBot();
        $bot->domain_monitoring_id = $monitoring->id;
        $bot->user_id = $userId;
        $bot->token = Str::limit(md5(Carbon::now() . $userId), 40);
        $bot->save();

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
        $project = DomainMonitoring::findOrFail($id);
        DomainMonitoring::httpCheck($project);

        return Redirect::back();
    }

    /**
     * @param $timing
     */
    public function checkLinkCrone($timing)
    {
        $projects = DomainMonitoring::where('timing', '=', $timing)->get();
        foreach ($projects as $project) {
            DomainMonitoring::httpCheck($project);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {
        if (strlen($request->option) > 0) {
            DomainMonitoring::where('id', $request->id)->update([
                $request->name => $request->option,
            ]);
            return response()->json([]);
        }
        return response()->json([], 400);
    }
}
