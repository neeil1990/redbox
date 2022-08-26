<?php


namespace App\Classes\Position;


use App\Classes\Position\Engine\Google;
use App\Classes\Position\Engine\Yandex;
use App\MonitoringKeyword;

class PositionStore
{
    private $model;
    private $save;

    public function __construct(MonitoringKeyword $query, $save = true)
    {
        $this->model = $query;
        $this->save = $save;
    }

    public function save()
    {
        $save = $this->save;
        $query = $this->model->query;
        $project = $this->model->project;
        $engines = $project->searchengines;

        foreach ($engines as $engine){

            $response = null;
            if($engine->engine == 'yandex')
                $response = (new Yandex($project->url, $query, $engine->lr, $save))->handle();

            if($engine->engine == 'google')
                $response = (new Google($project->url, $query, $engine->lr, $save))->handle();

            if($response){

                $this->model->positions()->create([
                    'monitoring_searchengine_id' => $engine->id,
                    'position' => $response["position"],
                    'url' => strtolower($response["url"]),
                ]);
            }
        }

        return true;
    }

}
