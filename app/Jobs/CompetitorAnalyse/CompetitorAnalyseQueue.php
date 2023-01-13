<?php

namespace App\Jobs\CompetitorAnalyse;

use App\SearchCompetitors;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CompetitorAnalyseQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $request;

    private $userId;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $request, int $userId)
    {
        $this->request = $request;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $analysis = new SearchCompetitors();
        $analysis->setUserId($this->userId);
        $analysis->setPhrases($this->request['phrases']);
        $analysis->setRegion($this->request['region']);
        $analysis->setCount($this->request['count']);
        $analysis->setPageHash($this->request['pageHash']);
        $analysis->analyseList();
    }
}
