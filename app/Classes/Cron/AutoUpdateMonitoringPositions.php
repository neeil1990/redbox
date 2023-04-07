<?php


namespace App\Classes\Cron;

use App\Classes\Monitoring\Queues\PositionsDispatch;
use App\Jobs\AutoUpdatePositionQueue;

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

        $queue = new PositionsDispatch($project['user_id'], 'position_low');
        foreach ($project->keywords as $query)
            $queue->addQueryWithRegion($query, $engine);

        $queue->dispatch();
    }
}
