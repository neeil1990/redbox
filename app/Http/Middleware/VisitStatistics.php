<?php

namespace App\Http\Middleware;

use App\MainProject;
use App\VisitStatistic;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Throwable;

class VisitStatistics
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $targetController = class_basename(Route::current()->controller);
            $project = MainProject::where('controller', 'like', '%' . $targetController . '%')->first();
            if (isset($project)) {
                $config = explode("\r\n", $project->controller);
                $callAction = explode('\\', Route::current()->action['controller']);
                $callAction = explode('@', end($callAction))[1];

                $forbidden = [];
                $access = [];
                foreach ($config as $action) {
                    $action = str_replace($targetController, '', $action);
                    if ($action != '' && str_contains($action, '!')) {
                        $forbidden[] = str_replace('!', '', $action);
                    } else if($action != '' && str_contains($action, '@')) {
                        $access[] = str_replace('@', '', $action);
                    }
                }

                if (in_array($callAction, $forbidden)) {
                    return $next($request);
                } else if (in_array($callAction, $access)) {
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

        } catch (Throwable $e) {

        }

        return $next($request);

    }
}
