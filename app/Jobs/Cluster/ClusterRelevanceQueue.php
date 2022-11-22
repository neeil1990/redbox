<?php

namespace App\Jobs\Cluster;

use App\Cluster;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ClusterRelevanceQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cluster;

    protected $query;

    protected $key;

    protected $phrase;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Cluster $cluster, string $query, $key, $phrase)
    {
        $this->cluster = $cluster;
        $this->query = $query;
        $this->key = $key;
        $this->phrase = $phrase;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $clusterArrays = new \App\ClusterQueue();
        $this->cluster->getXml()->setQuery($this->query);
        $this->cluster->getXml()->setCount(3);
        $response = $this->cluster->getXml()->getXMLResponse();
        Log::debug($this->query, [$response]);
        $clusterArrays->json = json_encode([
            $this->key => [
                $this->phrase => [
                    'relevance' => $response
                ]
            ]
        ]);

        $clusterArrays->progress_id = $this->cluster->getProgressId();
        $clusterArrays->save();
    }
}
