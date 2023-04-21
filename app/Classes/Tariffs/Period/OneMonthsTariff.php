<?php


namespace App\Classes\Tariffs\Period;


class OneMonthsTariff extends PeriodTariff
{
    public function __construct()
    {
        $this->percent = 5;
        $this->months = 1;
    }

    public function code(): string
    {
        return 'oneMonths';
    }
}
