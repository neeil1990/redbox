<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('website', function ($attribute, $value) {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return true;
            } else {
                return false;
            }
        }, __('Invalid landing page link.'));

        Validator::extend('not_website', function ($attribute, $value) {
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                return true;
            } else {
                return false;
            }
        }, __('The phrase cannot be a link.'));

        Schema::defaultStringLength(191);
    }
}
