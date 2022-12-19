<?php

namespace App\ViewComposers;

use App\News;
use App\NewsNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CountUnreadNewsComposer
{
    public function compose(View $view)
    {
        $notification = NewsNotification::where('user_id', '=', Auth::id())->first();
        if (isset($notification)) {
            $count = News::where('created_at', '>=', $notification->last_check)->get()->count();
        } else {
            $count = News::all()->count();
        }

        $view->with(compact('count'));
    }
}
