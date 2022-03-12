<?php


namespace App\Classes\Tariffs\Period;


use App\Classes\Tariffs\Interfaces\Period;

class ThreeMonthsTariff extends PeriodTariff implements Period
{
    public function __construct()
    {
        $this->percent = 10;
        $this->months = 3;
    }

    public function code(): string
    {
        return 'threeMonths';
    }
}
