<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringColumn extends Model
{
    protected $fillable = ['user_id', 'column', 'state'];
}
