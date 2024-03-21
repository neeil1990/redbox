<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UsersStatistic extends Model
{
    protected $fillable = ['monitoring_project'];

    protected $casts = [
        'monitoring_project' => 'collection',
    ];

    public function scopeSelectMonitoringProjectsToday($query)
    {
        $query->select('monitoring_project')->whereDate('created_at', Carbon::now());
    }
}
