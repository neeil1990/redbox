<?php

namespace App\Jobs;

use App\Relevance;
use App\RelevanceHistory;
use App\UsersJobs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RelevanceAnalysisQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    private $request;

    private $userId;

    private $historyId;

    private $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $request, $historyId, $link = false, $phrase = false, $type = 'full')
    {
        $this->request = $request;
        $this->userId = $userId;
        $this->historyId = $historyId;
        $this->type = $type;

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

        if ($this->type == 'full') {

            $relevance->getMainPageHtml();

            if ($this->request['type'] == 'phrase') {
                $relevance->analysisByPhrase($this->request, false);

            } elseif ($this->request['type'] == 'list') {
                $relevance->analysisByList($this->request);
            }

        } elseif ($this->type == 'mainPage') {

            $info = RelevanceHistory::where('id', '=', $this->request['id'])->first();

            $relevance->getMainPageHtml();
            $relevance->setSites($info->sites);

        } elseif ($this->type == 'competitors') {

            $info = RelevanceHistory::where('id', '=', $this->request['id'])->first();

            $relevance->setMainPage(gzuncompress(base64_decode($info->html_main_page)));
            $relevance->setDomains($info->sites);
            $relevance->parseSites();
        }

        $relevance->analysis($this->userId, $this->historyId);

        UsersJobs::where('user_id', '=', $this->userId)->decrement('count_jobs');
    }

    /**
     * @return void
     */
    public function failed()
    {

    }
}
