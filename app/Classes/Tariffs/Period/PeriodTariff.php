<?php


namespace App\Classes\Tariffs\Period;


use App\Classes\Tariffs\Interfaces\Period;
use Carbon\Carbon;

abstract class PeriodTariff implements Period
{
    protected $price;
    protected $days = 0;
    protected $months = 0;
    protected $percent = 0;

    public function name(): string
    {
        return __("$this->months Months with discount $this->percent%");
    }

    abstract public function code(): string;

    public function setPrice(int $price)
    {
        $this->price = $price;

        return $this;
    }

    public function setDays(int $days)
    {
        $this->days = $days;

        return $this;
    }

    public function setMonths(int $months)
    {
        $this->months = $months;

        return $this;
    }

    public function percent(): int
    {
        return $this->percent;
    }

    public function price(): int
    {
        $days = $this->days();

        return $this->price * $days;
    }

    public function discount(): int
    {
        $discount = round(($this->price()/100) * $this->percent());

        return $discount;
    }

    public function total(): int
    {
        $price = $this->price();

        $discount = $this->discount();

        $total = $price - $discount;

        return $total;
    }

    public function days(): int
    {
        $carbon = Carbon::now();

        if($this->months)
           $days = $carbon->add('months', $this->months)->diffInDays();
        else
           $days = $carbon->add('days', $this->days)->diffInDays();

        return $days;
    }
}
