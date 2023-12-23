<?php

namespace App\Jobs\Monitoring;

use App\MonitoringCompetitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MonitoringCompetitorsQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $request;

    public int $targetId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $targetId)
    {
        $this->request = $request;
        $this->targetId = $targetId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        MonitoringCompetitor::getCompetitors($this->request, $this->targetId);
    }
}
