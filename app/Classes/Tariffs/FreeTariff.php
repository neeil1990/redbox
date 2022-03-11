<?php


namespace App\Classes\Tariffs;


use App\Classes\Tariffs\Interfaces\Settings;
use App\Classes\Tariffs\Period\ThreeMonthsTariff;
use App\Classes\Tariffs\Settings\FreeSettings;

class FreeTariff extends Tariff
{
    public $name = 'Free Tariff!';
    protected $code = 'free';

    public function __construct()
    {
        parent::__construct(new ThreeMonthsTariff());
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
        return new FreeSettings($this->code());
    }

    public function code(): string
    {
        return $this->code;
    }
}
