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
        }elseif ($lang = $request->input('lang', 'ru')){
            App::setLocale($lang);
        }else{
            if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
                $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                if($lang){
                    $acceptLang = ['ru'];
                    $lang = in_array($lang, $acceptLang) ? $lang : 'en';
                    App::setLocale($lang);
                }
            }
        }

        return $next($request);
    }
}
