<?php


namespace App\Helpers;

use Illuminate\Support\Collection;

class CollectionHelper
{
    public static function appendBefore(Collection $collection, $key, $val, $before)
    {
        return $collection->flatMap(function ($item, $idx) use ($key, $val, $before) {
            return $idx === $before ? [$key => $val, $idx => $item] : [$idx => $item];
        });
    }
}
