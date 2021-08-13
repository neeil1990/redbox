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
    }
}
