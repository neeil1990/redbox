<?php


namespace App\Classes\Tariffs;


use App\Classes\Tariffs\Interfaces\Settings;
use App\Classes\Tariffs\Settings\MaximumSettings;


class MaximumTariff extends Tariff
{
    public function name(): string
    {
        $name = 'Maximum Tariff';

        return $name;
    }

    public function settings(): Settings
    {
        return new MaximumSettings();
    }
}
