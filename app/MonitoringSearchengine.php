<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringSearchengine extends Model
{
    protected $fillable = [
        'engine',
        'lr',
        'auto_update',
        'time',
        'weekdays',
        'monthday',
    ];

    protected $casts = [
        'weekdays' => 'array',
    ];

    public function location()
    {
        return $this->hasOne(Location::class, 'lr', 'lr');
    }

    public function positions()
    {
        return $this->hasMany(MonitoringPosition::class);
    }

    public function project()
    {
        return $this->belongsTo(MonitoringProject::class, 'monitoring_project_id');
    }
}
