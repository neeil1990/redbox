<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TariffSetting extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'message',
    ];

    public function fields()
    {
        return $this->hasMany(TariffSettingValue::class);
    }

}
