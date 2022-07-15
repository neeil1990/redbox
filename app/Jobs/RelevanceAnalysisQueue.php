<?php

namespace App\Jobs;

use App\Relevance;
use App\RelevanceHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class RelevanceAnalysisQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    private $request;

    private $userId;

    private $historyId;

    private $returnState;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $request, $historyId, $link = false, $phrase = false, $returnState = false)
    {
        $this->request = $request;
        $this->userId = $userId;
        $this->historyId = $historyId;
        $this->returnState = $returnState;

        if ($link != false) {
            $this->request['link'] = $link;
            $this->request['phrase'] = $phrase;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $relevance = new Relevance($this->request, true);
        $relevance->getMainPageHtml();

        if ($this->request['type'] == 'phrase') {
            $relevance->analysisByPhrase($this->request);

        } elseif ($this->request['type'] == 'list') {
            $relevance->analysisByList($this->request);
        }

        $relevance->analysis($this->userId, $this->historyId);

        if ($this->returnState) {
            RelevanceHistory::where('id', '=', $this->historyId)->update(['state' => 1]);
        }
    }

    /**
     * @return void
     */
    public function failed()
    {

    }
}
