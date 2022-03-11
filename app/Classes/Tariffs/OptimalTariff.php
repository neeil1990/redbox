<?php


namespace App\Classes\Tariffs;


use App\Classes\Tariffs\Interfaces\Settings;
use App\Classes\Tariffs\Period\ThreeMonthsTariff;
use App\Classes\Tariffs\Settings\OptimalSettings;

class OptimalTariff extends Tariff
{
    public $name = 'Optimal tariff!';
    protected $code = 'optimal';

    public function __construct()
    {
        parent::__construct(new ThreeMonthsTariff());

        $this->price = 10;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return Settings
     */
    public function settings(): Settings
    {
        return new OptimalSettings($this->code());
    }

    public function code(): string
    {
        return $this->code;
    }
}
