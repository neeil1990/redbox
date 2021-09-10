<?php

namespace App\Http\Middleware;

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LastOnline
{
    public function handle($request, Closure $next)
    {
        if (auth()->guest()) {
            return $next($request);
        }
        $user = Auth::user();

        if ($user->last_online_at->diffInHours(now()) !== 0)
        {
            DB::table("users")
                ->where("id", Auth::id())
                ->update(["last_online_at" => now()]);
        }

        return $next($request);
    }
}
