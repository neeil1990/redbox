<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    protected $dates = ['last_activity'];

}
