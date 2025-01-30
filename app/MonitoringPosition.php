<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MonitoringPosition extends Model
{
    protected $fillable = ['monitoring_searchengine_id', 'position', 'url', 'target', 'created_at', 'updated_at'];

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

    public function scopeDateRange($query, array $dates = null)
    {
        $start = Carbon::now()->subMonth();
        $end = Carbon::now();

        if($dates){

            $start = Carbon::create($dates[0]);
            $end = Carbon::create($dates[1]);
        }

        return $query->where(DB::raw('DATE(created_at)'), '>=', $start)
            ->where(DB::raw('DATE(created_at)'), '<=', $end);
    }

    public function scopeDateFind($query, array $dates = null)
    {
        $start = Carbon::create($dates[0]);
        $end = Carbon::create($dates[1]);

        return $query->where(DB::raw('DATE(created_at)'), '=', $start)
            ->orWhere(DB::raw('DATE(created_at)'), '=', $end);
    }

    public function scopeWhereEngine($query, int $id)
    {
        return $query->where('monitoring_searchengine_id', $id);
    }
}
