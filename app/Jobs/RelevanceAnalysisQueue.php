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
use Illuminate\Support\Facades\Log;

class RelevanceAnalysisQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    private $request;

    private $userId;

    private $historyId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $request, $historyId, $link = false, $phrase = false)
    {
        $this->request = $request;
        $this->userId = $userId;
        $this->historyId = $historyId;

        if ($link != false) {
            $this->request['link'] = $link;
            $this->request['phrase'] = $phrase;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $relevance = new TestRelevance($this->request['link'], $this->request['phrase'], $this->request['separator']);
            $relevance->getMainPageHtml();

            if ($this->request['type'] == 'phrase') {

                $xml = new SimplifiedXmlFacade(100, $this->request['region']);
                $xml->setQuery($this->request['phrase']);
                $xmlResponse = $xml->getXMLResponse();

                $relevance->removeIgnoredDomains(
                    $this->request['count'],
                    $this->request['ignoredDomains'],
                    $xmlResponse,
                    false
                );
                $relevance->parseSites($xmlResponse);

            } elseif ($this->request['type'] == 'list') {

                $sitesList = str_replace("\r\n", "\n", $this->request['siteList']);
                $sitesList = explode("\n", $sitesList);

                foreach ($sitesList as $item) {
                    $relevance->domains[] = [
                        'item' => str_replace('www.', '', mb_strtolower(trim($item))),
                        'ignored' => false,
                        'position' => count($relevance->domains) + 1
                    ];
                }
                $relevance->parseSites();
            }

            $relevance->analysis($this->request, $this->userId, $this->historyId);

        } catch (\Exception $exception) {
            // игнорируем ошибку: packets out of order
            if (
                strpos($exception->getFile(), '/vendor/laravel/framework/src/Illuminate/Database/Connection.php') === false &&
                $exception->getLine() != 664
            ) {
                $object = RelevanceHistory::where('id', '=', $this->historyId)->first();

                $object->state = -1;

                $object->save();
                Log::debug('message', [
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine()
                ]);
            }
        }

    }

    /**
     * @return void
     */
    public function failed()
    {

    }
}
