<?php

namespace App\Http\Controllers;

use App\DomainMonitoring;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Spatie\Async\Pool;

class DomainMonitoringController extends Controller
{
    public $counter;

    public function index()
    {
        $projects = DomainMonitoring::where('user_id', '=', Auth::id())->get();
        $countProjects = count($projects);
        if ($countProjects === 0) {
            return $this->createView();
        }

        return view('domain-monitoring.index', compact('projects', 'countProjects'));
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
        $project = DomainMonitoring::findOrFail($id);
        DomainMonitoring::httpCheck($project);

        return Redirect::back();
    }

    /**
     * @param $timing
     * @throws Exception
     */
    public function checkLinkCrone($timing)
    {
        $pool = Pool::create();

        foreach (range(1, 5) as $i) {
            $pool[] = async(function () {
                    self::random(Str::random(3));
                })->then(function () {
                });
        }
        await($pool);
        dd($pool);
        for ($i = 1; $i <= 5; $i++) {
            shell_exec("php " . base_path('artisan') . " httpCheck {$timing} {$i} &");
        }
    }

    /**
     * @param $i
     * @throws Exception
     */
    public function random($i): void
    {
        Log::debug($i . ' start', [Carbon::now()]);
        sleep(random_int(1, 3));
        Log::debug($i . ' end', [Carbon::now()]);
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
