<?php

namespace App\ViewComposers;

use App\MenuItemsPosition;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MenuComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();
        if (isset($user)) {
            $result = MenuItemsPosition::sortMenu();
            $modules = [];

            foreach ($result as $key => $item) {
                if (array_key_exists('configurationInfo', $item)) {
                    foreach ($item as $k => $elem) {
                        if ($k === 'configurationInfo') {
                            $modules[$key]['configurationInfo'] = $elem;
                            continue;
                        }

                        $access = (is_null($elem['access'])) ? [] : $elem['access'];

                        if ($user->hasRole($access)) {
                            $modules[$key][] = [
                                'id' => $elem['id'],
                                'title' => __($elem['title']),
                                'description' => $elem['description'],
                                'link' => $elem['link'],
                                'icon' => $elem['icon'],
                            ];
                        }
                    }
                } else {
                    $access = (is_null($item['access'])) ? [] : $item['access'];
                    if ($user->hasRole($access)) {
                        $modules[] = [
                            'id' => $item['id'],
                            'title' => __($item['title']),
                            'description' => $item['description'],
                            'link' => $item['link'],
                            'icon' => $item['icon'],
                        ];
                    }
                }
            }

            $modules = collect($modules)->toArray();

            $view->with(compact('modules'));
        }
    }
}
