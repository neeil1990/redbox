<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role, ...$guards)
    {
        if (Auth::guard($guards)->guest()) {

            throw new AuthenticationException(
                'Unauthenticated.', $guards, route('login')
            );
        }

        $roles = is_array($role)
            ? $role
            : explode('|', $role);

        if (! Auth::guard($guards)->user()->hasAnyRole($roles)) {
            throw UnauthorizedException::forRoles($roles);
        }

        return $next($request);
    }
}
