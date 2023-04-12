<?php

namespace App\Http\Middleware;

use App\MainProject;
use App\VisitStatistic;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class VisitStatistics
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $project = MainProject::where('controller', 'like', '%' . class_basename(Route::current()->controller) . '%')->first();
            if (isset($project)) {
                $actions = explode("\r\n", $project->controller);
                $callAction = explode('\\', Route::current()->action['controller']);
                $callAction = end($callAction);

                if (in_array($callAction, $actions)) {
                    VisitStatistic::updateOrCreate([
                        'project_id' => $project->id,
                        'user_id' => Auth::id(),
                        'date' => Carbon::now()->toDateString(),
                    ])->increment('refresh_page_counter');
                } else {
                    VisitStatistic::updateOrCreate([
                        'project_id' => $project->id,
                        'user_id' => Auth::id(),
                        'date' => Carbon::now()->toDateString(),
                    ])->increment('actions_counter');
                }
            }

        } catch (\Throwable $e) {

        }

        return $next($request);

    }
}
