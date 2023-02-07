<?php

namespace App\Jobs;

use App\Classes\Services\XmlRiver;
use App\MonitoringKeyword;
use App\MonitoringOccurrence;
use App\MonitoringSearchengine;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OccurrenceQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $queryId;
    protected $regionId;
    protected $query;
    protected $lr;

    public $timeout = 0;
    public $retryAfter = 1;

    /**
     * Create a new job instance.
     *
     * @param MonitoringKeyword $keyword
     * @param MonitoringSearchengine $regions
     */
    public function __construct(MonitoringKeyword $keyword, MonitoringSearchengine $regions)
    {
        $this->queryId = $keyword->id;
        $this->query = $keyword->query;
        $this->regionId = $regions->id;
        $this->lr = $regions->lr;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $query = $this->query;

        MonitoringOccurrence::updateOrCreate(
            ['monitoring_keyword_id' => $this->queryId, 'monitoring_searchengine_id' => $this->regionId],
            ['base' => $this->getBase($query), 'phrasal' => $this->getPhrasal($query), 'exact' => $this->getExact($query)]
        );
    }

    private function getBase($query)
    {
        return (new XmlRiver($query, $this->lr))->get();
    }

    private function getPhrasal($query)
    {
        $query = '"[' . $query . ']"';
        return (new XmlRiver($query, $this->lr))->get();
    }

    private function getExact($query)
    {
        $query = '"[!' . $query . ']"';
        return (new XmlRiver($query, $this->lr))->get();
    }
}
