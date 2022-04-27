<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringKeyword extends Model
{
    protected $fillable = ['query', 'page'];

    public function project()
    {
        return $this->belongsTo(MonitoringProject::class, 'monitoring_project_id');
    }

    public function positions()
    {
        return $this->hasMany(MonitoringPosition::class);
    }
}
