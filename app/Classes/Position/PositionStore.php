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

    public function saveByQuery(MonitoringKeyword $model, $engine = null)
    {
        $query = $model->query;
        $project = $model->project;

        $engines = ($engine) ? [$engine] : $project->searchengines;

        foreach ($engines as $engine){

            $response = $this->getEngine($engine->engine, [
                'domain' => $project->url,
                'query' => $query,
                'lr' => $engine->lr,
            ])->handle();

            $this->save($model, [
                'monitoring_searchengine_id' => $engine->id,
                'position' => (isset($response["position"])) ? $response["position"] : 101,
                'url' => (isset($response["url"])) ? strtolower($response["url"]) : null,
            ]);
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
