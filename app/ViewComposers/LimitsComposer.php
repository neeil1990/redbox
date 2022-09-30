<?php

namespace App\ViewComposers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LimitsComposer
{

    public function compose(View $view)
    {
        /** @var User $user */
        $user = Auth::user();

        $tariff = $user->tariff();
        dd($tariff);
        $name = ($tariff) ? $tariff->name() : null;

        $tariffs = [];
        if (isset($tariff)) {
            $tariffs = $tariff->getAsArray()['settings'];
        }

        $view->with(compact('user', 'name', 'tariffs'));
    }

}
