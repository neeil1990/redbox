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

    protected $stage;

    protected $count;

    protected $total;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Cluster $cluster, int $stage = 1, $countPhrases = false)
    {
        $this->cluster = $cluster;
        $this->stage = $stage;
        $this->count = $this->cluster->getProgressCurrentCount();
        $this->total = !$countPhrases ? $this->cluster->getProgressTotal() : $countPhrases;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->total === 0) {
            return;
        } else if ($this->total !== $this->count) {
            if ($this->stage === 1) {
                $type = 'cluster_first_stage';
            } else {
                $type = 'cluster_second_stage';
            }
            dispatch(new WaitClusterAnalyseQueue($this->cluster, $this->stage, $this->total))->onQueue($type)->delay(Carbon::now()->addSeconds(5));
        } else if ($this->stage === 1) {
            $this->cluster->secondStage();
        } else if ($this->stage === 2) {
            $this->cluster->finallyStage();
        }
    }
}
