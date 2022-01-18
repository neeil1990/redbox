<?php


namespace App\Classes\Tariffs;


use App\Classes\Tariffs\Interfaces\Settings;
use App\Classes\Tariffs\Settings\FreeSettings;

class FreeTariff extends Tariff
{

    /**
     * @return string
     */
    public function name(): string
    {
        $name = 'Free Tariff';

        return $name;
    }

    /**
     * @return Settings
     */
    public function settings(): Settings
    {
        return new FreeSettings();
    }

}
