<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

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

    public function prices()
    {
        return $this->hasMany(MonitoringKeywordPrice::class);
    }

    /**
     * Expands model fields, add new columns with position from monitoring positions
     *
     * @param $query
     * @param string $separator
     * @param string $prefix
     * @param string $postfix
     * @param Collection $ids
     * @return mixed
     */
    public function scopeAddLastPositions($query, string $separator, string $prefix, string $postfix, Collection $ids)
    {
        foreach ($ids as $id)
            $query->addSubSelect(implode($separator, [$prefix, $id, $postfix]), MonitoringPosition::select('position')
                ->whereColumn('monitoring_keyword_id', 'monitoring_keywords.id')
                ->WhereEngine($id)
                ->latest());

        return $query;
    }

    /**
     * @param $query
     * @param string $name
     * @param Collection $ids
     * @param Carbon $carbon
     * @return mixed
     */
    public function scopeAddLastPositionsOfMonth($query, string $name, Collection $ids, Carbon $carbon)
    {
        foreach ($ids as $id)
            $query->addSubSelect(implode('_', [$name, $id, $carbon->format('Y-m')]), MonitoringPosition::select('position')
                ->whereColumn('monitoring_keyword_id', 'monitoring_keywords.id')
                ->WhereEngine($id)
                ->whereMonth('created_at', $carbon->month)
                ->whereYear('created_at', $carbon->year)
                ->latest());

        return $query;
    }

    public function scopeJoinGroup($query)
    {
        return $query->leftJoin('monitoring_groups', 'monitoring_keywords.monitoring_group_id', '=', 'monitoring_groups.id');
    }
}
