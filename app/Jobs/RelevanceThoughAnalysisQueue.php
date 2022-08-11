<?php

namespace App\Jobs;

use App\ProjectRelevanceThough;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

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
            Log::debug('Первая стадия сквозного анализа ' . $this->mainId);
            ProjectRelevanceThough::thoughAnalyse($this->items, $this->mainId, $this->countRecords);

            dispatch(new RelevanceThoughAnalysisQueue([
                'stage' => 2,
                'countRecords' => $this->countRecords,
                'mainId' => $this->mainId,
                'thoughId' => $this->thoughId,
            ]));

        } elseif ($this->stage == 2) {
            Log::debug('Вторая стадия сквозного анализа ' . $this->mainId);

            $though = ProjectRelevanceThough::where('project_relevance_history_id', '=', $this->mainId)->first();
            $thoughWords = json_decode(gzuncompress(base64_decode($though->though_words)), true);

            ProjectRelevanceThough::searchWordWorms($thoughWords, $this->mainId);

            dispatch(new RelevanceThoughAnalysisQueue([
                'stage' => 3,
                'countRecords' => $this->countRecords,
                'mainId' => $this->mainId,
                'thoughId' => $this->thoughId,
            ]));

        } elseif ($this->stage == 3) {
            Log::debug('Третья стадия сквозного анализа ' . $this->mainId);

            $though = ProjectRelevanceThough::where('project_relevance_history_id', '=', $this->mainId)->first();
            $wordWorms = json_decode(gzuncompress(base64_decode($though->word_worms)), true);
            ProjectRelevanceThough::calculateFinalResult($wordWorms, $this->countRecords, $this->mainId);
        }

    }

    /**
     * @return void
     */
    public function failed()
    {

    }
}
