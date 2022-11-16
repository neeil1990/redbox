<?php

namespace App\Jobs;

use App\Cluster;
use Carbon\Carbon;
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
            dispatch(new WaitClusterAnalyse($this->cluster))->onQueue('wait_cluster')->delay(Carbon::now()->addSeconds(5));
        } else {
            $this->cluster->setRiverResults();
        }
    }
}
