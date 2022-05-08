<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringGroup extends Model
{
    protected $fillable = ['user_id', 'type', 'name'];
}
