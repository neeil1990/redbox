<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringPosition extends Model
{
    protected $fillable = ['monitoring_searchengine_id', 'position', 'target'];

    public function engine()
    {
        return $this->belongsTo(MonitoringSearchengine::class, 'monitoring_searchengine_id');
    }
}
