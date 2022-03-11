<?php


namespace App\Classes\Tariffs\Period;


use App\Classes\Tariffs\Interfaces\Period;

class OneDayTariff extends PeriodTariff implements Period
{
    public function __construct()
    {
        $this->months = 0;
        $this->days = 1;
    }

    public function name(): string
    {
        return __("$this->days Day with discount $this->percent%");
    }

    public function code(): string
    {
        return "oneDay";
    }
}
