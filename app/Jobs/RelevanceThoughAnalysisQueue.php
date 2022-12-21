<?php

namespace App\Jobs;

use App\Models\Relevance\ProjectRelevanceThough;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RelevanceThoughAnalysisQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $items;

    public $mainId;

    public $thoughId;

    public $stage;

    public $state;

    public $config;

    public $object;

    public $countRecords;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->countRecords = $config['countRecords'];
        $this->thoughId = $config['thoughId'];
        $this->mainId = $config['mainId'];
        $this->stage = $config['stage'];

        if ($this->stage == 1) {
            $this->items = $config['items'];
        }

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->stage == 1) {
            ProjectRelevanceThough::thoughAnalyse($this->items, $this->mainId, $this->countRecords);

            dispatch(new RelevanceThoughAnalysisQueue([
                'stage' => 2,
                'countRecords' => $this->countRecords,
                'mainId' => $this->mainId,
                'thoughId' => $this->thoughId,
            ]));

        } elseif ($this->stage == 2) {

            $though = ProjectRelevanceThough::where('project_relevance_history_id', '=', $this->mainId)->first();
            $thoughWords = json_decode(gzuncompress(base64_decode($though->though_words)), true);

            ProjectRelevanceThough::searchWordForms($thoughWords, $this->mainId);

            dispatch(new RelevanceThoughAnalysisQueue([
                'stage' => 3,
                'countRecords' => $this->countRecords,
                'mainId' => $this->mainId,
                'thoughId' => $this->thoughId,
            ]));

        } elseif ($this->stage == 3) {

            $though = ProjectRelevanceThough::where('project_relevance_history_id', '=', $this->mainId)->first();
            $wordWorms = json_decode(gzuncompress(base64_decode($though->word_worms)), true);
            ProjectRelevanceThough::calculateFinalResult($wordWorms, $this->mainId);
        }
    }

    /**
     * @return void
     */
    public function failed()
    {

    }
}
