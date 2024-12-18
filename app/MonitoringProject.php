<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoringProject extends Model
{
    protected $guarded = [];

    protected $top3;
    protected $top5;
    protected $top10;
    protected $top30;
    protected $top100;
    protected $middle;
    protected $mastered;
    protected $mastered_percent;
    protected $mastered_info;
    protected $words;

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('admin', 'approved', 'status');
    }

    public function admin()
    {
        return $this->belongsToMany(User::class)->withPivot('admin', 'approved')->wherePivot('admin', 1);
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

    public static function getLastDates(MonitoringProject $project): array
    {
        $keywords = $project->keywords->pluck('id');
        $lastChecks = [];

        foreach ($project->searchengines->pluck('id') as $region) {
            $positions = MonitoringPosition::select(DB::raw('*, DATE(created_at) as dateOnly'))
                ->where('monitoring_searchengine_id', $region)
                ->whereIn('monitoring_keyword_id', $keywords)
                ->orderBy('id', 'desc')
                ->with('engine')
                ->first();

            if (isset($positions)) {
                $lastChecks[] = $positions->toArray();
            }
        }

        return $lastChecks;
    }

    public static function getLastDate(MonitoringProject $project, $region, $dateOnly = false)
    {
        $keywords = $project->keywords->pluck('id');

        $result = MonitoringPosition::select(DB::raw('*, DATE(created_at) as dateOnly'))
            ->where('monitoring_searchengine_id', $region)
            ->whereIn('monitoring_keyword_id', $keywords)
            ->orderBy('id', 'desc')
            ->with('engine')
            ->first();

        if (isset($result)) {
            $result->toArray();
        } else {
            return [];
        }

        if ($dateOnly) {
            return $result['dateOnly'];
        }

        return [$result];
    }

    public static function getLastDateByWords(array $keywords, $region): ?MonitoringPosition
    {
        return MonitoringPosition::select(DB::raw('*, DATE(created_at) as dateOnly'))
            ->where('monitoring_searchengine_id', $region)
            ->whereIn('monitoring_keyword_id', array_column($keywords, 'id'))
            ->orderBy('id', 'desc')
            ->with('engine')
            ->first();
    }
}
