<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringGroup extends Model
{
    protected $fillable = ['monitoring_project_id', 'type', 'name'];
}
