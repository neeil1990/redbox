<?php

namespace App\Http\Middleware;

use Closure;

class SetTeamContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        apply_global_team_permissions();

        return $next($request);
    }
}
