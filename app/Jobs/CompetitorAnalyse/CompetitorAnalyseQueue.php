<?php

namespace App\Jobs\CompetitorAnalyse;

use App\SearchCompetitors;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CompetitorAnalyseQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var mixed
     */
    private $request;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $analysis = new SearchCompetitors();
        $analysis->setPhrases($this->request['phrases']);
        $analysis->setRegion($this->request['region']);
        $analysis->setCount($this->request['count']);
        $analysis->setPageHash($this->request['pageHash']);
        $analysis->analyseList();
    }
}
