<?php

namespace App\Jobs\Cluster;

use App\Cluster;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class WaitClusterAnalyseQueue implements ShouldQueue
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
        if ($this->cluster->getProgressTotal() === 0) {
            return;
        } else if ($this->cluster->getProgressTotal() !== $this->cluster->getProgressCurrentCount()) {
            dispatch(new WaitClusterAnalyseQueue($this->cluster))->onQueue('cluster_wait')->delay(Carbon::now()->addSeconds(10));
        } else {
            $this->cluster->calculate();
        }
    }

    public function __sleep()
    {
        return [
            'cluster',
        ];
    }
}
