<?php


namespace App\Classes\Tariffs\Settings;


use App\Classes\Tariffs\Interfaces\Settings;
use App\TariffSettingValue;
use App\User;

class MaximumSettings extends SettingsAbstract implements Settings
{
    public function __construct(string $code, ?User $user = null)
    {
        $this->user = $user;
        $this->tariff = $code;
    }
}
