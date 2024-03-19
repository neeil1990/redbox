<?php

namespace App\Jobs\Relevance;

use App\Common;
use App\Relevance;
use App\RelevanceAnalyseResults;
use App\RelevanceProgress;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RelevanceAnalyseQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    public $request;

    public $exp;

    public $type;

    public $userId;

    public $relevance;

    public function __construct($request, $exp, $userId, $type)
    {
        $this->request = $request;
        $this->exp = $exp;
        $this->type = $type;
        $this->userId = $userId;
    }

    public function handle()
    {
        $this->relevance = new Relevance($this->request, $this->userId);

        if ($this->type === 'full') {

            $this->relevance->getMainPageHtml();
            if ($this->request['type'] == 'phrase') {
                $this->relevance->analysisByPhrase($this->request, $this->exp);
            } elseif ($this->request['type'] == 'list') {
                $this->relevance->analysisByList($this->request);
            }

        } else if ($this->type === 'competitors') {

            RelevanceProgress::editProgress(15, $this->request);

            $params = RelevanceAnalyseResults::where('user_id', '=', $this->userId)
                ->where('page_hash', '=', $this->request['pageHash'])
                ->first();
            $this->relevance->setMainPage($params->html_main_page);
            $this->relevance->setDomains($params->sites);
            $this->relevance->parseSites();

        } else if ($this->type === 'main') {

            RelevanceProgress::editProgress(15, $this->request);

            $params = RelevanceAnalyseResults::where('user_id', '=', $this->userId)
                ->where('page_hash', '=', $this->request['pageHash'])
                ->first();
            $this->relevance->getMainPageHtml();
            $this->relevance->setSites($params->sites);
        }

        $this->relevance->analysis();
    }
}
