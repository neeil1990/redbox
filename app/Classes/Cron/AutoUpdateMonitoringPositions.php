<?php


namespace App\Classes\Cron;

use App\Jobs\AutoUpdatePositionQueue;
use Illuminate\Support\Facades\Log;

class AutoUpdateMonitoringPositions
{
    private $engine;

    public function __construct($engine)
    {
        $this->engine = $engine;
    }

    public function __invoke()
    {
        $engine = $this->engine;
        $project = $engine->project;

        foreach ($project->keywords as $query)
            dispatch((new AutoUpdatePositionQueue($query, $engine))->onQueue('position_low'));

        //Log::debug($this->model);
    }
}
