<?php


namespace App\Classes\Tariffs\Settings;


use App\TariffSettingValue;

abstract class SettingsAbstract
{
    protected $tariff;
    protected $settings;

    public function get(): array
    {
        $this->settings = [];

        $settings = TariffSettingValue::where('tariff', $this->tariff)->get();
        foreach ($settings as $setting){
            $this->settings[$setting->property->code] = $setting->value;
        }

        return $this->settings;
    }
}
