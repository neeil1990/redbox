<?php

namespace App\Http\Middleware;

use App\MainProject;
use App\User;
use App\VisitStatistic;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

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
        if (!Auth::check()) {
            return $next($request);
        }

        try {
            $controllerAction = last(explode('\\', Route::current()->action['controller']));
            if ($controllerAction === 'PublicController@updateStatistics') {
                return $next($request);
            }

            $targetController = class_basename(Route::current()->controller);
            $project = MainProject::where('controller', 'like', "%" . $targetController . '%')->first();

            if (empty($project)) {
                return $next($request);
            }

            $config = explode("\n", $project->controller);
            $callAction = last(explode('@', Route::current()->action['controller']));

            foreach ($config as $action) {
                if (explode('@', $action)[0] !== $targetController && explode('!', $action)[0] !== $targetController) {
                    continue;
                }
                $action = str_replace($targetController, '', $action);
                if ($action === '') {
                    continue;
                }

                if ($this->findAction('!', $action, $callAction)) {
                    $this->updateOrCreateVisitStatistic($project, 'actions_counter');

                    return $next($request);

                } else if ($this->findAction('@', $action, $callAction)) {
                    $this->updateOrCreateVisitStatistic($project, 'refresh_page_counter');

                    return $next($request);
                }
            }

            return $next($request);

        } catch (\Throwable $e) {
            Log::debug('visit statistics error', [
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                'controller' => $targetController ?? null,
                'project' => $project ?? null,
                'action' => $action ?? null
            ]);
        }

        return $next($request);

    }

    private function updateOrCreateVisitStatistic($project, $incrementField)
    {
        VisitStatistic::updateOrCreate([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'date' => Carbon::now()->toDateString(),
        ])->increment($incrementField);
    }

    private function findAction($rule, $action, $callAction): bool
    {
        return Str::contains($action, $rule) && $callAction === str_replace($rule, '', $action);
    }
}
