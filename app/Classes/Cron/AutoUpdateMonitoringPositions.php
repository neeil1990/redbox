<?php


namespace App\Classes\Cron;


use App\Jobs\AutoUpdatePositionQueue;
use Illuminate\Support\Facades\Log;

class AutoUpdateMonitoringPositions
{
    private $model;

    public function __construct($engine)
    {
        $this->model = $engine;
    }

    public function __invoke()
    {
        //Log::debug($this->model);
        dispatch((new AutoUpdatePositionQueue($this->model))->onQueue('position_low'));
    }
}
