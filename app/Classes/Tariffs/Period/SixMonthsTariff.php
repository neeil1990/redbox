<?php


namespace App\Classes\Tariffs\Period;


use App\Classes\Tariffs\Interfaces\Period;

class SixMonthsTariff extends PeriodTariff implements Period
{
    public function __construct()
    {
        $this->percent = 20;
        $this->months = 6;
    }

    public function code(): string
    {
        return 'sixMonths';
    }
}
