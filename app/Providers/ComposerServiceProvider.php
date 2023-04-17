<?php

namespace App\Providers;


use App\ViewComposers\CountUnreadNewsComposer;
use App\ViewComposers\DescriptionComposer;
use App\ViewComposers\LimitsComposer;
use App\ViewComposers\MenuComposer;
use App\ViewComposers\StatisticsComposer;
use App\ViewComposers\UserPanelComposer;
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
        view()->composer('component.card', DescriptionComposer::class);
        view()->composer('users.panel', UserPanelComposer::class);
        view()->composer('navigation.menu-right', UserPanelComposer::class);
        view()->composer('navigation.menu-right', LimitsComposer::class);
        view()->composer('navigation.sidebar', MenuComposer::class);
        view()->composer('navigation.menu', CountUnreadNewsComposer::class);
        view()->composer('layouts.app', StatisticsComposer::class);
    }
}
