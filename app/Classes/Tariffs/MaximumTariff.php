<?php


namespace App\Classes\Tariffs;


use App\Classes\Tariffs\Interfaces\Settings;
use App\Classes\Tariffs\Period\ThreeMonthsTariff;
use App\Classes\Tariffs\Settings\MaximumSettings;


class MaximumTariff extends Tariff
{
    public $name = 'Maximum Tariff!';
    protected $code = 'maximum';

    public function __construct()
    {
        parent::__construct(new ThreeMonthsTariff());
        $this->price = 20;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function settings(): Settings
    {
        return new MaximumSettings($this->code());
    }

    public function code(): string
    {
        return $this->code;
    }
}
