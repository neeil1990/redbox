<?php

namespace App\Classes\Monitoring;

use App\MonitoringDataTableColumnsProject;
use \Illuminate\Support\Collection;

class ProjectDataTableUpdateDB
{
    protected $model;
    protected $percents = [
        'top3' => 3,
        'top5' => 5,
        'top10' => 10,
        'top30' => 30,
        'top100' => 100,
    ];

    public function __construct(Collection $project)
    {
        $this->model = $project;
    }

    public function save()
    {
        foreach ($this->model as $model) {

           $keywords = $model->keywords()->get();

           if($keywords->isEmpty())
               continue;

           $arResult = $this->calculateTop($keywords, $model);

           $arResult['words'] = $model->keywords->count();

           if(count($arResult) > 0)
                MonitoringDataTableColumnsProject::updateOrCreate(
                    ['monitoring_project_id' => $model->id],
                    $arResult
                );
        }
    }

    private function calculateTop(Collection $keywords, &$model)
    {
        $arResult = [];
        $positions = $this->getLastPositionsByKeywords($keywords, $model);

        $positionsForLastDay = $positions->first();
        $positionsForPenultimateDay = $positions->last();

        foreach ($this->percents as $name => $percent){
            $last = Helper::calculateTopPercentByPositions($positionsForLastDay, $percent);
            $prev = Helper::calculateTopPercentByPositions($positionsForPenultimateDay, $percent);

            $arResult[$name] = $last;

            $diff = 'diff_' . $name;
            $arResult[$diff] = Helper::differentTopPercent($last, $prev);
        }

        $arResult['middle'] = ($positionsForLastDay->isNotEmpty()) ? round($positionsForLastDay->sum() / $positionsForLastDay->count()) : 0;

        return $arResult;
    }

    private function getLastPositionsByKeywords(Collection $keywords, $model)
    {
        $first = collect([]);
        $last = collect([]);

        if($keywords->isEmpty())
            return collect([]);

        $regions = $model->searchengines()->get();

        foreach($keywords as $keyword){

            $lastPositions = $this->getLastPositionOfRegionsByKeyword($regions, $keyword);

            $first = $first->merge($lastPositions['first']);
            $last = $last->merge($lastPositions['last']);
        }

        return collect([$first, $last]);
    }

    private function getLastPositionOfRegionsByKeyword($regions, $keyword)
    {
        $positions = collect([
            'first' => collect([]),
            'last' => collect([]),
        ]);

        foreach ($regions as $region){
            $collection = $this->getLastPositionsOfRegionByKeyword($region, $keyword);
            if($collection->isNotEmpty()){
                $positions['first']->push($collection->first()->position);

                if($collection->count() > 1)
                    $positions['last']->push($collection->last()->position);
            }
        }

        return $positions;
    }

    public function getLastPositionsOfRegionByKeyword($region, $keyword)
    {
        $positions = $region->positions()
            ->whereNotNull('position')
            ->where('monitoring_keyword_id', $keyword->id)
            ->orderBy('created_at', 'desc')
            ->take(2)->get();

        return $positions;
    }
}
