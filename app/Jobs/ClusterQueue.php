<?php

namespace App\Jobs;

use App\Classes\Xml\RiverFacade;
use App\ClusterProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClusterQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $progress;

    protected $link;

    protected $key;

    protected $phrase;

    protected $region;

    protected $type;

    protected $targetPhrase;

    protected $percent;

    protected $progressId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($region, $progressId, $percent, $targetPhrase, $key, $phrase, $type)
    {
        $this->progressId = $progressId;
        $this->targetPhrase = $targetPhrase;
        $this->key = $key;
        $this->phrase = $phrase;
        $this->region = $region;
        $this->type = $type;
        $this->percent = $percent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $river = new RiverFacade($this->region);
            $river->setQuery($this->targetPhrase);
            $clusterArrays = new \App\ClusterQueue();
            $clusterArrays->json = json_encode([
                $this->key => [
                    $this->phrase => [
                        $this->type => $river->riverRequest($this->type === 'based')
                    ]
                ]
            ]);
            $clusterArrays->progress_id = $this->progressId;
            $clusterArrays->save();

            $this->progress = ClusterProgress::where('id', '=', $this->progressId)->update([
                'success' => DB::raw('success + 1'),
                'percent' => DB::raw("percent + $this->percent")
            ]);

        } catch (\Throwable $e) {
            Log::debug('cluster job error', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
