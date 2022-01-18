<?php


namespace App\Classes\Tariffs;


use App\Classes\Tariffs\Interfaces\Settings;
use App\Classes\Tariffs\Settings\OptimalSettings;

class OptimalTariff extends Tariff
{

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Optimal tariff!';
    }

    /**
     * @return Settings
     */
    public function settings(): Settings
    {
        return new OptimalSettings();
    }
}
