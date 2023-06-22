<?php

namespace App\Jobs\Relevance;

use App\Relevance;
use App\RelevanceHistory;
use App\TelegramBot;
use App\UsersJobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RelevanceHistoryQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $request;

    public $userId;

    public $historyId;

    public $type;

    public $relevance;

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

        if ($link !== false) {
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
        $this->relevance = new Relevance($this->request, $this->userId, true);

        try {
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
            UsersJobs::where('user_id', '=', $this->userId)->decrement('count_jobs');

        } catch (\Throwable $exception) {
            RelevanceHistory::where('id', '=', $this->request['id'])->update([
                'state' => '-1'
            ]);

            $this->relevance->saveError($exception);
        }

        die();
    }
}
