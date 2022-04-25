<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringProgect extends Model
{
    protected $fillable = ['name', 'url', 'status'];

    public function keywords(){

        return $this->hasMany(MonitoringKeyword::class);
    }

    public function competitors(){

        return $this->hasMany(MonitoringCompetitor::class);
    }

    public function searchengines(){

        return $this->hasMany(MonitoringSearchengine::class);
    }
}
