<?php

namespace App\Jobs;

use App\Events\MonitoringProjectCopyProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CopyKeywordsMonitoringProjectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $keyword;
    public $keywordIds;
    public $searchengineIds;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($keyword, $keywordIds, $searchengineIds)
    {
        $this->keyword = $keyword;
        $this->keywordIds = $keywordIds;
        $this->searchengineIds = $searchengineIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->keyword->positions as $position) {
            $newPosition = $position->replicate();
            $newPosition->monitoring_keyword_id = $this->keywordIds[$position->monitoring_keyword_id];
            $newPosition->monitoring_searchengine_id = $this->searchengineIds[$position->monitoring_searchengine_id];
            $newPosition->created_at = $position->created_at;
            $newPosition->updated_at = $position->updated_at;
            $newPosition->save();
        }

        foreach ($this->keyword->prices as $price) {
            $newPrice = $price->replicate();
            $newPrice->monitoring_keyword_id = $this->keywordIds[$price->monitoring_keyword_id];
            $newPrice->monitoring_searchengine_id = $this->searchengineIds[$price->monitoring_searchengine_id];
            $newPrice->save();
        }
    }
}
