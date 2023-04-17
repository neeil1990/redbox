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
        $colors = [];
        $time = [];

        if ($encode) {
            foreach ($summedCollection as $module) {
                $colors[] = $module->project->color;
                $labels[] = __($module->project->title);
                $counters[] = $module->actionsCounter + $module->refreshPageCounter;
            }

            return [
                'labels' => json_encode($labels),
                'counters' => json_encode($counters),
                'colors' => json_encode($colors),
            ];
        }

        foreach ($summedCollection as $module) {
            $colors[] = $module->project->color;
            $labels[$module->project->link] = __($module->project->title);
            $counters[] = ['actionsCounter' => $module->actionsCounter, 'refreshPageCounter' => $module->refreshPageCounter];
            $time[] = $module->time;
        }

        return [
            'labels' => $labels,
            'counters' => $counters,
            'colors' => $colors,
            'time' => $time,
        ];
    }
}
