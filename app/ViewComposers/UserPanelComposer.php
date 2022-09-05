<?php


namespace App\ViewComposers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserPanelComposer
{
    public function compose(View $view)
    {
        /** @var User $user */
        $user = Auth::user();

        $tariff = $user->tariff();
        $name = ($tariff) ? $tariff->name() : null;
        $tariffs = $tariff->getAsArray()['settings'];
        dd($tariff->getAsArray());

        $view->with(compact('user', 'name', 'tariffs'));
    }
}
