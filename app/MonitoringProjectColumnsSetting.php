<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringProjectColumnsSetting extends Model
{
    protected $fillable = ['monitoring_project_id', 'name', 'state'];
    protected $table = "monitoring_project_columns";
}
