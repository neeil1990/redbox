<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = ['sum', 'status', 'source'];

    public $statuses = [
        0 => 'Платеж не прошел',
        1 => 'Пополнение',
        2 => 'Расход'
    ];
}
