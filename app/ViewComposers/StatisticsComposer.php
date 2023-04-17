<?php

namespace App\ViewComposers;

use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class StatisticsComposer
{
    public function compose(View $view)
    {
        $controllerAction = last(explode('\\', Route::current()->action['controller']));

        $view->with(compact('controllerAction'));
    }
}
