<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TariffSettingUserValue extends Model
{
    protected $fillable = ['tariff_setting_value_id', 'user_id', 'value'];

    public function field()
    {
        return $this->belongsTo(TariffSettingValue::class, 'tariff_setting_value_id');
    }
}
