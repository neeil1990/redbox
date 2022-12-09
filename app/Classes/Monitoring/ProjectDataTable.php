<?php


namespace App\Classes\Monitoring;



use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProjectDataTable
{
    protected $model;

    private $first;
    private $last;

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
        }
    }

    private function calculateTopPercent(Collection $keywords, &$model)
    {
        $cache = new CacheOfUserForPosition($model);
        $positionCacheKey = $cache->getCacheKey();

        $positionsCache = Cache::remember($positionCacheKey, $cache->getCacheTime(), function () use ($keywords, $model) {
            return $this->getLastPositionsByKeywords($keywords, $model);
        });

        $positionsForLastDay = $positionsCache->first();
        $positionsForPenultimateDay = $positionsCache->last();

        $percents = [
            'top_three' => 3,
            'top_fifth' => 5,
            'top_ten' => 10,
            'top_thirty' => 30,
            'top_one_hundred' => 100,
        ];

        foreach ($percents as $name => $percent){
            $last = Helper::calculateTopPercentByPositions($positionsForLastDay, $percent);
            $preLast = Helper::calculateTopPercentByPositions($positionsForPenultimateDay, $percent);
            $model->$name = $last . Helper::differentTopPercent($last, $preLast);
        }

        $model->middle_position = ($positionsForLastDay->isNotEmpty()) ? round($positionsForLastDay->sum() / $positionsForLastDay->count()) : 0;
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
