<?php


namespace App\Classes\Tariffs\Period;


use App\Classes\Tariffs\Interfaces\Period;

class TwelveMonthsTariff extends PeriodTariff implements Period
{
    public function __construct()
    {
        $this->percent = 35;
        $this->months = 12;
    }

    public function code(): string
    {
        return 'twelveMonths';
    }
}
