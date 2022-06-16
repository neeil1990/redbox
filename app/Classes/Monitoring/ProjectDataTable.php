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

            $this->calculateTopPercent($keywords, $model);

            $positions = $this->getLastPositionsByKeywords($keywords);

            $model->middle_position = ($positions->isNotEmpty()) ? round($positions->sum() / $positions->count()) : 0;
        }
    }

    private function calculateTopPercent(Collection $keywords, &$model)
    {
        $positions = $this->getLastPositionsByKeywords($keywords);
        $pre_positions = $this->getPreLastPositionsByKeywords($keywords);

        $percents = [
            'top_three' => 3,
            'top_fifth' => 5,
            'top_ten' => 10,
            'top_thirty' => 30,
            'top_one_hundred' => 100,
        ];

        foreach ($percents as $name => $percent){

            $last = Helper::calculateTopPercentByPositions($positions, $percent);
            $preLast = Helper::calculateTopPercentByPositions($pre_positions, $percent);
            $model->$name = $last . Helper::differentTopPercent($last, $preLast);
        }
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
