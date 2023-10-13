<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringKeyword extends Model
{
    protected $fillable = ['monitoring_group_id', 'target', 'query', 'page'];

    public function project()
    {
        return $this->belongsTo(MonitoringProject::class, 'monitoring_project_id');
    }

    public function group()
    {
        return $this->belongsTo(MonitoringGroup::class, 'monitoring_group_id');
    }

    public function positions()
    {
        return $this->hasMany(MonitoringPosition::class);
    }

    public function price()
    {
        return $this->hasOne(MonitoringKeywordPrice::class);
    }

}
