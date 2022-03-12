<?php


namespace App\Classes\Tariffs\Settings;


use App\Classes\Tariffs\Interfaces\Settings;
use App\TariffSettingValue;

class MaximumSettings implements Settings
{
    protected $tariff;
    protected $settings;

    public function __construct(string $code)
    {
        $this->tariff = $code;
    }

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
