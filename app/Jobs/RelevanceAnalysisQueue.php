<?php

namespace App\Jobs;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\TestRelevance;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

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
        $this->separator = $separator;
        $this->region = $region;
        $this->phrase = $phrase;
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
        try {
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

        } catch (\Exception $e) {
            Log::debug('debug', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }

    }
}
