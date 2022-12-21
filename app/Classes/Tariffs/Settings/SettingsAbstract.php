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
        foreach ($settings as $setting) {

            $this->settings[$setting->property->code] = [
                'name' => $setting->property->name,
                'message' => $this->replaceMsg($setting->property->message, $setting->value),
                'value' => $setting->value,
            ];
        }

        return collect($this->settings)->sortBy('position')->toArray();
    }

    protected function replaceMsg(?string $str, $val)
    {
        if (!$str)
            return null;

        $str = __($str);

        $str = str_replace('{TARIFF}', $this->tariff, $str);
        $str = str_replace('{VALUE}', $val, $str);

        return $str;
    }
}
