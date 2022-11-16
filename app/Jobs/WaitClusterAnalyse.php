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
        Log::debug('dispatch WaitClusterAnalyse __construct');
        $this->cluster = $cluster;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('dispatch WaitClusterAnalyse handle');
        $count = $this->cluster->getProgressCurrentCount();

        if ($this->cluster->getProgressTotal() !== $count) {
            Log::debug('wait');
            $this->release(10);
        } else {
            $this->cluster->setRiverResults();
        }
    }
}
