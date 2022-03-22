<?php


namespace App\Classes\Tariffs\Settings;


use App\Classes\Tariffs\Interfaces\Settings;
use App\TariffSettingValue;

class MaximumSettings extends SettingsAbstract implements Settings
{
    public function __construct(string $code)
    {
        $this->tariff = $code;
    }
}
