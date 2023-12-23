<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Throwable;

class MenuItemsPosition extends Model
{
    protected $table = 'menu_items_position';

    protected $guarded = [];

    public static function sortMenu(): array
    {
        if (User::isUserAdmin()) {
            $items = MainProject::orderBy('position', 'asc')->get();
        } else {
            $items = MainProject::where('show', '=', 1)->orderBy('position', 'asc')->get();
        }
        $items = $items->toArray();
        $config = MenuItemsPosition::where('user_id', '=', Auth::id())->first();

        if (isset($config)) {
            $oldPositions = json_decode($config->positions, true);
            $newPositions = [];

            foreach ($oldPositions as $item) {
                if (isset($item[0]) && $item[0]['dir']) {
                    $newPositions[$item[0]['dirName']]['configurationInfo'] = $item[0];
                    foreach ($item as $groupItem) {
                        if (isset($groupItem['dir'])) {
                            continue;
                        }
                        foreach ($items as $key => $elem) {
                            if ($elem['id'] == $groupItem['id']) {
                                $newPositions[$item[0]['dirName']][] = $elem;
                                unset($items[$key]);
                            }
                        }
                    }
                    continue;
                }
                foreach ($items as $key => $elem) {
                    if ($elem['id'] == $item['id']) {
                        $newPositions[] = $elem;
                        unset($items[$key]);
                    }
                }
            }

            if (count($items) > 0) {
                foreach ($items as $elem) {
                    $newPositions[] = $elem;
                }
            }

            return $newPositions;
        }

        return $items;
    }
}
