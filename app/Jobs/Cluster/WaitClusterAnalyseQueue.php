<?php

namespace App\Jobs\Cluster;

use App\Cluster;
use App\ClusterResults;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $result = ClusterResults::where('progress_id', '=', $this->cluster->getProgressId())->first();
        if (isset($result)) {
            exit();
        } else if ($this->cluster->getProgressTotal() !== $this->cluster->getProgressCurrentCount()) {
            dispatch(new WaitClusterAnalyseQueue($this->cluster))->onQueue('cluster_wait')->delay(Carbon::now()->addSeconds(10));
        } else {
            $this->cluster->calculate();
        }
    }
}
