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
            $positions = $this->getIsNotEmptyPositions($keywords);

            $model->top_three = $this->calculatePercentByPositions($positions, 3);
            $model->top_fifth = $this->calculatePercentByPositions($positions, 5);
            $model->top_ten = $this->calculatePercentByPositions($positions, 10);
            $model->top_thirty = $this->calculatePercentByPositions($positions, 30);
            $model->top_one_hundred = $this->calculatePercentByPositions($positions, 100);
        }
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

    private function getIsNotEmptyPositions(Collection $keywords)
    {
        $positions = collect([]);

        if($keywords->isEmpty())
            return $positions;

        foreach($keywords as $keyword){

            $position = $keyword->positions()->get();
            $lastPositionsForKeyword = $position->transform(function ($item){
                if(is_null($item->position))
                    $item->position = 1000;
                return $item;
            })->sortByDesc('id')->unique('monitoring_searchengine_id')->pluck('position');

            $positions = $positions->merge($lastPositionsForKeyword);
        }

        return $positions;
    }
}
