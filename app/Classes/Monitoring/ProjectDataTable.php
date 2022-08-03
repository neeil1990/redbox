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

            $positions = $this->getLastPositionsByKeywords($keywords, $model);

            $model->middle_position = ($positions->isNotEmpty()) ? round($positions->sum() / $positions->count()) : 0;
        }
    }

    private function calculateTopPercent(Collection $keywords, &$model)
    {
        $positions = $this->getLastPositionsByKeywords($keywords, $model);
        $pre_positions = $this->getPreLastPositionsByKeywords($keywords, $model);

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

    private function getLastPositionsByKeywords(Collection $keywords, $model)
    {
        $positions = collect([]);

        if($keywords->isEmpty())
            return $positions;

        foreach($keywords as $keyword){

            $regions = $model->searchengines()->get();

            $lastPositions = $this->getLastPositionOfRegionsByKeyword($regions, $keyword);

            $positions = $positions->merge($lastPositions);
        }

        return $positions;
    }

    private function getLastPositionOfRegionsByKeyword($regions, $keyword)
    {
        $positions = collect([]);

        foreach ($regions as $region){
            $positionModel = $this->getLastPositionOfRegionByKeyword($region, $keyword);
            if($positionModel)
                $positions->push($positionModel->position);
        }

        return $positions;
    }

    public function getLastPositionOfRegionByKeyword($region, $keyword)
    {
        return $region->positions()
            ->whereNotNull('position')
            ->where('monitoring_keyword_id', $keyword->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    private function getPreLastPositionsByKeywords(Collection $keywords, $model)
    {
        $pre_positions = collect([]);

        if($keywords->isEmpty())
            return $pre_positions;

        foreach($keywords as $keyword){

            $regions = $model->searchengines()->get();

            $positions = collect([]);
            foreach ($regions as $region){
                $positionModel = $this->getPenultimatePositionOfRegionByKeyword($region, $keyword);
                if($positionModel)
                    $positions->push($positionModel->position);
            }

            $pre_positions = $pre_positions->merge($positions);
        }

        return $pre_positions;
    }

    public function getPenultimatePositionOfRegionByKeyword($region, $keyword)
    {
        $lastPosition = $this->getLastPositionOfRegionByKeyword($region, $keyword);

        if(!$lastPosition)
            return null;

        return $region->positions()
            ->where('monitoring_keyword_id', $keyword->id)
            ->where(DB::raw('DATE(created_at)'), '<', $lastPosition->created_at->format('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
