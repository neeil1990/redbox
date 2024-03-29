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
            if (isset($value)) {
                if (!preg_match("~^(?:f|ht)tps?://~i", $value)) {
                    $value = "https://" . $value;
                }

                $link = parse_url($value);

                if (isset($link['host'])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }, __('Invalid link.'));

        Validator::extend('not_website', function ($attribute, $value) {
            $link = parse_url($value);
            if (!isset($link['host'])) {
                return true;
            } else {
                return false;
            }
        }, __('The phrase cannot be a link.'));

        Schema::defaultStringLength(191);
    }
}
