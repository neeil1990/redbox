<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringProjectSettings extends Model
{
    protected $fillable = ['monitoring_project_id', 'name', 'value'];
}
