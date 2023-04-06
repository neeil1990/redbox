<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringLimit extends Model
{
    protected $fillable = ['user_id', 'counter', 'date'];
}
