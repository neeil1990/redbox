<?php


namespace App\ViewComposers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserPanelComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();
        $view->with(compact('user'));
    }
}
