<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public static function getLastDates(MonitoringProject $project): array
    {
        $allWords = MonitoringKeyword::where('monitoring_project_id', $project->id)->get(['id'])->toArray();
        $keywordsId = array_column($allWords, 'id');

        $lastChecks = [];
        foreach ($project->searchengines->pluck('id') as $region) {
            $positions = MonitoringPosition::select(DB::raw('*, DATE(created_at) as dateOnly'))
                ->where('monitoring_searchengine_id', $region)
                ->whereIn('monitoring_keyword_id', $keywordsId)
                ->orderBy('id', 'desc');

            dump($positions->first());
//            $lastChecks[] = $positions->first()->toArray();
        }
        dd(1);
        return $lastChecks;
    }
}
