<?php


namespace App\Classes\Tariffs\Settings;


use App\Classes\Tariffs\Interfaces\Settings;
use App\User;

class OptimalSettings extends SettingsAbstract implements Settings
{
    public function __construct(string $code, ?User $user = null)
    {
        $this->user = $user;
        $this->tariff = $code;
    }
}
