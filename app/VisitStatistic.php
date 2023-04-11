<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VisitStatistic extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function project(): HasOne
    {
        return $this->hasOne(MainProject::class, 'id', 'project_id');
    }
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public static function getModulesInfo($summedCollection, $encode = true): array
    {
        $labels = [];
        $counters = [];

        if ($encode) {
            foreach ($summedCollection as $module) {
                $labels[] = __($module->project->title);
                $counters[] = $module->counter;
            }

            return [
                'labels' => json_encode($labels),
                'counters' => json_encode($counters)
            ];
        }

        foreach ($summedCollection as $module) {
            $labels[$module->project->link] = __($module->project->title);
            $counters[] = $module->counter;
        }

        return [
            'labels' => $labels,
            'counters' => $counters
        ];
    }
}
