<?php

namespace App\Jobs;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\RelevanceHistory;
use App\TestRelevance;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RelevanceAnalysisQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    private $link;

    private $separator;

    private $region;

    private $phrase;

    private $count;

    private $ignoredDomains;

    private $request;

    private $userId;

    private $historyId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($link, $phrase, $separator, $region, $count, $ignoredDomains, $userId, $request, $historyId)
    {
        $this->link = $link;
        $this->phrase = $phrase;
        $this->separator = $separator;
        $this->region = $region;
        $this->count = $count;
        $this->ignoredDomains = $ignoredDomains;
        $this->request = $request;
        $this->userId = $userId;
        $this->historyId = $historyId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $relevance = new TestRelevance($this->link, $this->phrase, $this->separator);
        $relevance->getMainPageHtml();

        $xml = new SimplifiedXmlFacade(100, $this->region);
        $xml->setQuery($this->phrase);
        $xmlResponse = $xml->getXMLResponse();

        $relevance->removeIgnoredDomains(
            $this->count,
            $this->ignoredDomains,
            $xmlResponse,
            false
        );
        $relevance->parseSites($xmlResponse);
        $relevance->analysis($this->request, $this->userId, $this->historyId);
    }

    public function failed()
    {
        $object = RelevanceHistory::where('id', '=', $this->historyId)->first();

        $object->state = -1;

        $object->save();
    }
}
