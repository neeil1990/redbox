<?php


namespace App\Classes\Tariffs\Period;


use App\Classes\Tariffs\Interfaces\Period;

class FiveDaysTariff extends PeriodTariff implements Period
{
    public function __construct()
    {
        $this->percent = 5;
        $this->months = 0;
        $this->days = 5;
    }

    public function name(): string
    {
        return __("$this->days Days with discount $this->percent%");
    }

    public function code(): string
    {
        return "fiveDays";
    }
}
