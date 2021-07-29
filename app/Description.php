<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Description extends Model
{
    protected $fillable = ['code', 'lang', 'position'];

    public function getRouteKeyName()
    {
        return 'code';
    }

    /**
     * Получить пользователя, владеющего данным телефоном.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
