<?php

namespace App\Classes\Monitoring;

use App\MonitoringDataTableColumnsProject;
use App\MonitoringProject;

class ProjectDataTableUpdateDB
{
    protected $model;
    protected $keywords;
    protected $positions;
    protected $budget = null;

    protected $percents = [
        'top3' => 3,
        'top5' => 5,
        'top10' => 10,
        'top30' => 30,
        'top100' => 100,
    ];

    public function __construct(MonitoringProject $project)
    {
        $this->model = $project;
        $this->keywords = $project->keywords;
        $this->positions = $this->getPreviousAndLastPosition();
        $this->budget = $project['budget'];
    }

    public function save()
    {
        if(empty($this->model))
            return null;

       $arResult = $this->percent();

       $mastered = $this->mastered();

       $arResult['mastered'] = $mastered->total();
       $arResult['mastered_percent'] = $mastered->percentOf($this->budget);
       $arResult['mastered_info'] = collect([
           'top1' => $mastered->top1(),
           'top3' => $mastered->top3(),
           'top5' => $mastered->top5(),
           'top10' => $mastered->top10(),
           'top20' => $mastered->top20(),
           'top50' => $mastered->top50(),
           'top100' => $mastered->top100(),
           'total' => $mastered->total(),
       ]);

       $arResult['words'] = $this->keywords->count();

       if(count($arResult) > 0)
            MonitoringDataTableColumnsProject::updateOrCreate(
                ['monitoring_project_id' => $this->model['id']],
                $arResult
            );
    }

    private function mastered()
    {
        $latest = $this->positions->first();
        return new MasteredPositions($latest);
    }

    private function percent()
    {
        $arResult = [];

        $latest = $this->positions->first()->pluck('position');
        $previous = $this->positions->last()->pluck('position');

        foreach ($this->percents as $name => $percent){
            $last = Helper::calculateTopPercentByPositions($latest, $percent);
            $prev = Helper::calculateTopPercentByPositions($previous, $percent);

            $arResult[$name] = $last;

            $diff = 'diff_' . $name;
            $arResult[$diff] = Helper::differentTopPercent($last, $prev);
        }

        $arResult['middle'] = ($latest->isNotEmpty()) ? round($latest->sum() / $latest->count()) : 0;

        return $arResult;
    }

    private function getPreviousAndLastPosition()
    {
        $latest = collect([]);
        $previous = collect([]);

        foreach($this->keywords as $key){
            $dateOfLastPosition = $key->positions()->select('created_at')->orderBy('created_at', 'desc')->first();
            if(empty($dateOfLastPosition))
                continue;

            $latestCollect = $key->positions()->whereDate('created_at', $dateOfLastPosition['created_at']->toDateString())->orderBy('created_at', 'desc')->get()->unique('monitoring_searchengine_id');
            $previousCollect = $key->positions()->whereDate('created_at', $dateOfLastPosition['created_at']->subDay()->toDateString())->orderBy('created_at', 'desc')->get()->unique('monitoring_searchengine_id');

            if($latestCollect->isNotEmpty())
                $latest = $latest->merge($latestCollect);

            if($previousCollect->isNotEmpty())
                $previous = $previous->merge($previousCollect);
        }

        return collect([$latest, $previous]);
    }
}
