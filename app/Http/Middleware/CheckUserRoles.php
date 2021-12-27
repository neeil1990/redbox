<?php

namespace App\Http\Middleware;

use App\MainProject;
use Closure;
use \Spatie\Permission\Exceptions\UnauthorizedException;

class CheckUserRoles
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
        $route = MainProject::where('link', $request->url())->first();

        if((isset($route->access) && $request->user()->hasRole($route->access)) || $route === null)
            return $next($request);

        throw new UnauthorizedException(403, __('User does not have the right roles.'));
    }
}
