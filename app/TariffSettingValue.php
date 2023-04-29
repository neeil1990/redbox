<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TariffSettingValue extends Model
{
    protected $fillable = ['tariff_setting_id', 'tariff', 'value'];

    public function property()
    {
        return $this->belongsTo(TariffSetting::class, 'tariff_setting_id');
    }

    public function userValues()
    {
        return $this->hasMany(TariffSettingUserValue::class);
    }
}
