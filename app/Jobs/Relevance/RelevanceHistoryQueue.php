<?php

namespace App\Jobs\Relevance;

use App\Common;
use App\Relevance;
use App\RelevanceHistory;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RelevanceHistoryQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $request;

    public $userId;

    public $historyId;

    public $type;

    public $relevance;

    public function __construct($userId, $request, $historyId, $link = false, $phrase = false, $type = 'full')
    {
        $this->userId = $userId;
        $this->request = $request;
        $this->historyId = $historyId;
        $this->type = $type;

        if ($link !== false) {
            $this->request['link'] = $link;
            $this->request['phrase'] = $phrase;
        }
    }

    public function handle()
    {
        $jobs = Common::analyseRelevanceJobs();

        if ($jobs->count_relevance <= 1 || ($jobs->count_relevance == 2 && $jobs->another == 0)) {
            $this->relevance = new Relevance($this->request, $this->userId, true);
            if ($this->type == 'full') {
                $this->relevance->getMainPageHtml();

                if ($this->request['type'] == 'phrase') {
                    $this->relevance->analysisByPhrase($this->request, false);

                } elseif ($this->request['type'] == 'list') {
                    $this->relevance->analysisByList($this->request);
                }

            } elseif ($this->type == 'mainPage') {
                $info = RelevanceHistory::where('id', '=', $this->request['id'])->first();

                $this->relevance->getMainPageHtml();
                $this->relevance->setSites($info->sites);

            } elseif ($this->type == 'competitors') {
                $info = RelevanceHistory::where('id', '=', $this->request['id'])->first();

                $this->relevance->setMainPage(gzuncompress(base64_decode($info->html_main_page)));
                $this->relevance->setDomains($info->sites);
                $this->relevance->parseSites();
            }

            $this->relevance->analysis($this->historyId);
        } else {
            RelevanceHistoryQueue::dispatch(
                $this->userId,
                $this->request,
                $this->historyId,
                $this->request['link'] ?? false,
                $this->request['phrase'] ?? false,
                $this->type
            )
                ->onQueue($this->job->getQueue())
                ->onConnection('database')
                ->delay(Carbon::now()->addSeconds(10));
        }
    }
}
