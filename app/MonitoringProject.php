<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringProject extends Model
{
    protected $fillable = ['name', 'url', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function keywords()
    {
        return $this->hasMany(MonitoringKeyword::class);
    }

    public function competitors()
    {
        return $this->hasMany(MonitoringCompetitor::class);
    }

    public function searchengines()
    {
        return $this->hasMany(MonitoringSearchengine::class);
    }

    public function groups()
    {
        return $this->hasMany(MonitoringGroup::class);
    }

    public function backlinks()
    {
        return $this->hasMany(ProjectTracking::class);
    }

    public function dates()
    {
        return $this->hasMany(MonitoringChangesDate::class);
    }
}
