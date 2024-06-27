<?php


namespace App\Classes\AnalyzeRelevance;


use App\Http\Controllers\RelevanceController;
use App\Jobs;

class RelevanceQueues
{
    protected $job;

    public function __construct()
    {
        $this->job = new Jobs();
    }

    public function all()
    {
        return $this->job([RelevanceController::HIGH_QUEUE, RelevanceController::MEDIUM_QUEUE, RelevanceController::NORMAL_QUEUE]);
    }

    public function high()
    {
        return $this->job([RelevanceController::HIGH_QUEUE]);
    }

    public function medium()
    {
        return $this->job([RelevanceController::MEDIUM_QUEUE]);
    }

    public function normal()
    {
        return $this->job([RelevanceController::NORMAL_QUEUE]);
    }

    private function job(array $queue)
    {
        $objects = collect([]);
        $queues = $this->job->whereIn('queue', $queue)->pluck('payload');
        $commands = $queues->pluck('data');

        foreach($commands as $command)
        {
            $obj = unserialize($command['command']);
            $objects->push($obj);
        }

        return $objects;
    }
}
