<?php


namespace App\Classes\Monitoring;


use App\MonitoringPosition;
use App\MonitoringProject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use \Illuminate\Support\Collection;

class ProjectDataTable
{
    protected $model;

    public function __construct(Collection $project)
    {
        $this->model = $project;
    }

    public function handle()
    {
        $this->setName();
        $this->setUrl();
        $this->setSearchEngines();
        $this->setKeywords();
        $this->setTopPercentKeywords();

        return $this->getData();
    }

    /**
     * @return Collection
     */
    protected function getData(): Collection
    {
        return $this->model;
    }

    protected function setName()
    {
        foreach ($this->model as $model) {

            $model->name = '<a href="'. route('monitoring.show', $model->id) .'">'. $model->name .'</a>';
        }
    }

    protected function setUrl()
    {
        foreach ($this->model as $model) {

            $model->url = '<a href="http://'. $model->url .'" target="_blank">'. $model->url .'</a>';
        }
    }

    protected function setSearchEngines()
    {
        foreach ($this->model as $model) {

            $model->load(['searchengines' => function ($query) {
                $query->groupBy('engine');
            }]);

            $model->searches = $model->searchengines->pluck('engine')->map(function ($item){
                return '<i class="fab fa-'. $item .' fa-sm"></i>';
            })->implode(' | ');
        }
    }

    protected function setKeywords()
    {
        foreach ($this->model as $model) {
            $model->count = $model->keywords()->count();
        }
    }

    protected function setTopPercentKeywords()
    {
        foreach ($this->model as $model) {

            $keywords = $model->keywords()->get();
            $positions = $this->getLastPositionsByKeywords($keywords);
            $pre_positions = $this->getPreLastPositionsByKeywords($keywords);

            $top_three = $this->calculatePercentByPositions($positions, 3);
            $top_three_pre = $this->calculatePercentByPositions($pre_positions, 3);
            $model->top_three = $top_three . $this->differentTopPercent($top_three, $top_three_pre);

            $top_fifth = $this->calculatePercentByPositions($positions, 5);
            $top_fifth_pre = $this->calculatePercentByPositions($pre_positions, 5);
            $model->top_fifth = $top_fifth . $this->differentTopPercent($top_fifth, $top_fifth_pre);

            $top_ten = $this->calculatePercentByPositions($positions, 10);
            $top_ten_pre = $this->calculatePercentByPositions($pre_positions, 10);
            $model->top_ten = $top_ten . $this->differentTopPercent($top_ten, $top_ten_pre);

            $top_thirty = $this->calculatePercentByPositions($positions, 30);
            $top_thirty_pre = $this->calculatePercentByPositions($pre_positions, 30);
            $model->top_thirty = $top_thirty . $this->differentTopPercent($top_thirty, $top_thirty_pre);

            $top_one_hundred = $this->calculatePercentByPositions($positions, 100);
            $top_one_hundred_pre = $this->calculatePercentByPositions($pre_positions, 100);
            $model->top_one_hundred = $top_one_hundred . $this->differentTopPercent($top_one_hundred, $top_one_hundred_pre);

            $model->middle_position = ($positions->isNotEmpty()) ? round($positions->sum() / $positions->count()) : 0;
        }
    }

    private function differentTopPercent($a, $b)
    {
        $total = $a - $b;

        if(!$total || !$b)
            return '';

        if($total > 0){
            $total = ' (+'. $total .')';
        }else{
            $total = ' ('. $total .')';
        }

        return $total;
    }

    private function calculatePercentByPositions(Collection $positions, int $desired)
    {
        if($positions->isEmpty())
            return 0;

        $itemsCount = $positions->count();
        $desiredCount = $positions->filter(function ($val) use ($desired){
            return $val <= $desired;
        })->count();

        $totalPercent = round(($desiredCount / $itemsCount) * 100, 2);

        return $totalPercent;
    }

    private function getLastPositionsByKeywords(Collection $keywords)
    {
        $positions = collect([]);

        if($keywords->isEmpty())
            return $positions;

        foreach($keywords as $keyword){

            $position = $keyword->positions()->get();
            $lastPositionsForKeyword = $position->transform(function ($item){
                if(is_null($item->position))
                    $item->position = 101;
                return $item;
            })->sortByDesc('id')->unique('monitoring_searchengine_id')->pluck('position');

            $positions = $positions->merge($lastPositionsForKeyword);
        }

        return $positions;
    }

    private function getPreLastPositionsByKeywords(Collection $keywords)
    {
        $pre_positions = collect([]);

        if($keywords->isEmpty())
            return $pre_positions;

        foreach($keywords as $keyword){

            $positions = $keyword->positions()->get();

            $engines = [];
            foreach ($positions as $position)
                $engines[$position->monitoring_searchengine_id][] = $position;

            foreach ($engines as $engine){

                if((isset($engine[count($engine) - 2])))
                    $pre_positions = $pre_positions->merge($engine[count($engine) - 2]->position);
            }
        }

        return $pre_positions;
    }
}
