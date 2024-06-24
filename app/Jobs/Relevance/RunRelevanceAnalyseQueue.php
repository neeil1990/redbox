<?php

namespace App\Jobs\Relevance;

use App\Relevance;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RunRelevanceAnalyseQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $relevance;

    /**
     * Create a new job instance.
     *
     * @param Relevance $relevance
     */
    public function __construct(Relevance $relevance)
    {
        $this->relevance = $relevance;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->relevance->analysis();
    }
}
