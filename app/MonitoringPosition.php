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

    public function keyword()
    {
        return $this->belongsTo(MonitoringKeyword::class, 'monitoring_keyword_id');
    }

    public function getDateAttribute()
    {
        return $this->created_at->format('d.m.Y');
    }
}
