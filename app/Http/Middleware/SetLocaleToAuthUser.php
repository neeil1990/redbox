<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SetLocaleToAuthUser
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
        if (Auth::check()) {
            if($user = Auth::user())
                App::setLocale($user->lang);
        }else{
            $lang = (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) === "ru") ? "ru" : "en";
            App::setLocale($lang);
        }

        return $next($request);
    }
}
