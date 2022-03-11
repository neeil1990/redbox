<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TariffPay extends Model
{
    protected $dates = ['active_to'];
    protected $fillable = ['status', 'class_tariff', 'class_period', 'sum', 'active_to'];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
