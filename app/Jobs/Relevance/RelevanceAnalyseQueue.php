<?php

namespace App\Jobs\Relevance;

use App\Relevance;
use App\RelevanceAnalyseResults;
use App\RelevanceProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RelevanceAnalyseQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;

    public $exp;

    public $type;

    public $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $exp, $userId, $type)
    {
        $this->request = $request;
        $this->exp = $exp;
        $this->type = $type;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $relevance = new Relevance($this->request, $this->userId);
        if ($this->type === 'full') {
            $relevance->getMainPageHtml();

            if ($this->request['type'] == 'phrase') {
                $relevance->analysisByPhrase($this->request, $this->exp);
            } elseif ($this->request['type'] == 'list') {
                $relevance->analysisByList($this->request);
            }

        } else if ($this->type === 'competitors') {
            RelevanceProgress::editProgress(15, $this->request);

            $params = RelevanceAnalyseResults::where('user_id', '=', $this->userId)
                ->where('page_hash', '=', $this->request['pageHash'])
                ->first();
            $relevance->setMainPage($params->html_main_page);
            $relevance->setDomains($params->sites);
            $relevance->parseSites();
        } else if ($this->type === 'main') {
            RelevanceProgress::editProgress(15, $this->request);

            $params = RelevanceAnalyseResults::where('user_id', '=', $this->userId)
                ->where('page_hash', '=', $this->request['pageHash'])
                ->first();
            $relevance->getMainPageHtml();
            $relevance->setSites($params->sites);
        }

        $relevance->analysis();
    }
}
