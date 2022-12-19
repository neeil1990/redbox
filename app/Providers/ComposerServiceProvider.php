<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('component.card', '\App\ViewComposers\DescriptionComposer');
        view()->composer('users.panel', '\App\ViewComposers\UserPanelComposer');
        view()->composer('navigation.menu-right', '\App\ViewComposers\UserPanelComposer');
        view()->composer('navigation.menu-right', '\App\ViewComposers\LimitsComposer');
        view()->composer('navigation.sidebar', '\App\ViewComposers\MenuComposer');
        view()->composer('navigation.menu', '\App\ViewComposers\CountUnreadNewsComposer');
    }
}
