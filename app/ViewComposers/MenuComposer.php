<?php

namespace App\ViewComposers;

use App\MainProject;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MenuComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();
        if (isset($user)) {
            $result = MenuComposer::getProjects();
            $modules = [];

            foreach ($result as $item) {
                $access = (is_null($item['access'])) ? [] : $item['access'];
                if ($user->hasRole($access))
                    $modules[] = [
                        'id' => $item['id'],
                        'title' => __($item['title']),
                        'description' => $item['description'],
                        'link' => $item['link'],
                        'icon' => $item['icon'],
                    ];
            }

            $modules = collect($modules)->toArray();

            $view->with(compact('modules'));
        }
    }

    public static function getProjects(): array
    {
        if (User::isUserAdmin()) {
            $result = MainProject::orderBy('position', 'asc')->get();
        } else {
            $result = MainProject::where('show', '=', 1)->orderBy('position', 'asc')->get();
        }

        return $result->toArray();
    }
}
