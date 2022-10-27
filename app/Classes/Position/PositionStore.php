<?php


namespace App\Classes\Position;


use App\Classes\Position\Engine\Google;
use App\Classes\Position\Engine\Yandex;
use App\MonitoringKeyword;
use App\MonitoringSearchengine;
use Illuminate\Support\Arr;

class PositionStore
{
    private $save;

    public function __construct($saveAllResultIndexes = true)
    {
        $this->save = $saveAllResultIndexes;
    }

    public function saveBySearchEngines(MonitoringSearchengine $model)
    {
        $project = $model->project;

        foreach ($project->keywords as $keyword){

            $response = $this->getEngine($model->engine, [
                'domain' => $project->url,
                'query' => $keyword->query,
                'lr' => $model->lr,
            ])->handle();

            if($response)
                $this->save($keyword, [
                    'monitoring_searchengine_id' => $model->id,
                    'position' => $response["position"],
                    'url' => strtolower($response["url"]),
                ]);
        }

        return true;
    }

    public function saveByQuery(MonitoringKeyword $model)
    {
        $query = $model->query;
        $project = $model->project;
        $engines = $project->searchengines;

        foreach ($engines as $engine){

            $response = $this->getEngine($engine->engine, [
                'domain' => $project->url,
                'query' => $query,
                'lr' => $engine->lr,
            ])->handle();

            if($response){
                $this->save($model, [
                    'monitoring_searchengine_id' => $engine->id,
                    'position' => $response["position"],
                    'url' => strtolower($response["url"]),
                ]);
            }
        }

        return true;
    }

    private function save(MonitoringKeyword $model, $params = [])
    {
        $model->positions()->create($params);
    }

    private function getEngine($name, $params = [])
    {
        if(!Arr::has($params, ['domain', 'query', 'lr']))
            throw new \ErrorException('Params domain, query and lr is required.');

        switch ($name) {
            case "yandex":
                return new Yandex($params['domain'], $params['query'], $params['lr'], $this->save);
                break;
            case "google":
                return new Google($params['domain'], $params['query'], $params['lr'], $this->save);
                break;
            default:
                throw new \ErrorException('Search engine not exist.');
        }
    }

}
