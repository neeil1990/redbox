<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringPosition extends Model
{
    protected $fillable = ['monitoring_searchengine_id', 'position', 'target'];
}
