<?php

namespace App\Jobs;

use App\Cluster;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class WaitClusterAnalyse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cluster;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Cluster $cluster)
    {
        $this->cluster = $cluster;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $count = $this->cluster->getProgressCurrentCount();

        if ($this->cluster->getProgressTotal() !== $count) {
            Log::debug('wait');
            dispatch(new WaitClusterAnalyse($this->cluster))->onConnection('redis')->onQueue('wait_cluster');
        } else {
            $this->cluster->setRiverResults();
        }
    }
}
