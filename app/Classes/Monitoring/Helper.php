<?php


namespace App\Classes\Monitoring;


use Illuminate\Support\Collection;

class Helper
{

    static public function calculateTopPercentByPositions(Collection $positions, int $desired)
    {
        if($positions->isEmpty())
            return 0;

        $itemsCount = $positions->count();
        $desiredCount = $positions->filter(function ($val) use ($desired){
            return $val <= $desired;
        })->count();

        $totalPercent = round(($desiredCount / $itemsCount) * 100, 2);

        return $totalPercent;
    }

    static public function differentTopPercent($a, $b)
    {
        $total = $a - $b;

        if(!$total || !$b)
            return '';

        if($total > 0){
            $total = ' (+'. $total .')';
        }else{
            $total = ' ('. $total .')';
        }

        return $total;
    }
}
