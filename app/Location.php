<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['source', 'lr', 'name'];

    public function scopeYandex($query)
    {
        return $query->where('source', 'yandex');
    }

    public function scopeGoogle($query)
    {
        return $query->where('source', 'google');
    }
}
