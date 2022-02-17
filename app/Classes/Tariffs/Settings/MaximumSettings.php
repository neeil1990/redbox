<?php


namespace App\Classes\Tariffs\Settings;


use App\Classes\Tariffs\Interfaces\Settings;
use App\TariffSettingValue;

class MaximumSettings implements Settings
{
    protected $tariff = 'maximum';

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
