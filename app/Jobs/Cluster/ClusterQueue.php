<?php

namespace App\Jobs\Cluster;

use App\Classes\Xml\RiverFacade;
use App\Cluster;
use App\ClusterProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClusterQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $progress;

    protected $link;

    protected $key;

    protected $phrase;

    protected $region;

    protected $type;

    protected $percent;

    protected $progressId;

    protected $cluster;

    public function __construct(Cluster $cluster, $percent, $key, $phrase)
    {
        $this->progressId = $cluster->getProgressId();
        $this->region = $cluster->getRegion();
        $this->cluster = $cluster;
        $this->percent = $percent;
        $this->key = $key;
        $this->phrase = $phrase;
    }

    public function handle()
    {
        $clusterArrays = new \App\ClusterQueue();
        $river = new RiverFacade($this->region);

        if ($this->cluster->getSearchPhrases()) {
            $river->setQuery('"' . $this->phrase . '"');
            $phrase = $river->riverRequest(false);
        }

        if ($this->cluster->getSearchTarget()) {
            $river->setQuery('"!' . implode(' !', explode(' ', $this->phrase)) . '"');
            $target = $river->riverRequest(false);
        }

        $river->setQuery($this->phrase);
        $based = $river->riverRequest();

        if ($this->cluster->getSearchRelevance()) {
            $this->cluster->getXml()->setQuery("$this->phrase site:" . $this->cluster->getHost());
            $this->cluster->getXml()->setCount(10);
            $relevance = $this->cluster->getXml()->getXMLResponse($this->cluster->getSearchEngine());
            if (count($relevance) > 3) {
                $relevance = array_slice($relevance, 0, 3);
            }
        }

        $clusterArrays->json = json_encode([
            $this->key => [
                $this->phrase => [
                    'based' => $based,
                    'phrased' => $phrase ?? 0,
                    'target' => $target ?? 0,
                    'relevance' => $relevance ?? 0
                ]
            ]
        ]);

        $clusterArrays->progress_id = $this->progressId;
        $clusterArrays->save();

        ClusterProgress::where('id', '=', $this->progressId)->update([
            'success' => DB::raw("success + 1"),
            'percent' => DB::raw("percent + $this->percent")
        ]);
    }

    public function failed(\Throwable $exception)
    {
        Log::debug('cluster queue bug report', [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }
}
