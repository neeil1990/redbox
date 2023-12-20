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
        $user = $project->admin->first();

        $queue = new PositionsDispatch($user["id"], 'position_low');
        foreach ($project->keywords as $query)
            $queue->addQueryWithRegion($query, $engine);

        $queue->dispatch();
    }
}
