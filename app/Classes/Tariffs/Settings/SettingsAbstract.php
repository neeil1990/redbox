<?php


namespace App\Classes\Tariffs\Settings;


use App\TariffSettingValue;
use Illuminate\Database\Eloquent\Collection;

abstract class SettingsAbstract
{
    protected $user = null;
    protected $tariff;
    protected $settings;

    public function get(): array
    {
        $this->settings = [];

        $settings = TariffSettingValue::where('tariff', $this->tariff)->get();

        if($this->user)
            $this->changeSettingsValue($settings);

        foreach ($settings as $setting) {

            $this->settings[$setting->property->code] = [
                'name' => $setting->property->name,
                'message' => $this->replaceMsg($setting->property->message, $setting->value),
                'value' => $setting->value,
            ];
        }

        return collect($this->settings)->sortBy('position')->toArray();
    }

    private function changeSettingsValue(Collection $settings): void
    {
        $settingsUser = $this->user->tariffSettings()->get();
        foreach ($settingsUser as $item){
            if($setting = $settings->find($item->tariff_setting_value_id))
                $setting->value = $item->value;
        }
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
