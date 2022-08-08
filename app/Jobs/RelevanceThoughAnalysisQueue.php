<?php

namespace App\Jobs;

use App\ProjectRelevanceThough;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RelevanceThoughAnalysisQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $items;

    public $id;

    /**
     * @param Collection $items
     * @param int $id
     */
    public function __construct(Collection $items, int $id)
    {
        $this->items = $items->toArray();
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('start', [Carbon::now()->toTimeString()]);
        $countRecords = count($this->items);

        $though = ProjectRelevanceThough::thoughAnalyse($this->items, $this->id, $countRecords);
        $wordWorms = ProjectRelevanceThough::searchWordWorms($though);
        $resultArray = ProjectRelevanceThough::calculateFinalResult($wordWorms, $countRecords);

        $thoughResult = ProjectRelevanceThough::firstOrNew([
//            'result' => base64_encode(gzcompress(json_encode($resultArray), 9)),
            'project_relevance_history_id' => $this->id
        ]);

        $thoughResult->result = base64_encode(gzcompress(json_encode($resultArray), 9));
        $thoughResult->save();
        Log::debug('end', [Carbon::now()->toTimeString()]);
    }

    /**
     * @return void
     */
    public function failed()
    {

    }
}
