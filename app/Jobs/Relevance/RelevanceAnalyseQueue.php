<?php

namespace App\Jobs\Relevance;

use App\Relevance;
use App\RelevanceAnalyseResults;
use App\RelevanceProgress;
use App\UsersJobs;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RelevanceAnalyseQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $request;

    public $exp;

    public $type;

    public $userId;

    public Relevance $relevance;

    public function __construct($request, $exp, $userId, $type)
    {
        Log::info('construct');
        $this->request = $request;
        $this->exp = $exp;
        $this->type = $type;
        $this->userId = $userId;
        Log::info('construct end');
    }

    public function handle()
    {
//        Log::debug('loadavg', [sys_getloadavg()[0]]);
//        if (sys_getloadavg()[0] > 0.5) {
//            RelevanceAnalyseQueue::dispatch($this->request, $this->exp, $this->userId, $this->type)
//                ->onQueue($this->job->getQueue())
//                ->onConnection('database')
//                ->delay(Carbon::now()->addSeconds(10));
//        } else {
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
//        }
    }
}
