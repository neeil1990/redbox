<?php

namespace App\Jobs;

use App\Relevance;
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
            $relevance = new Relevance($this->request['link'], $this->request['phrase'], $this->request['separator'], true);
            $relevance->getMainPageHtml();

            if ($this->request['type'] == 'phrase') {
                $relevance->analysisByPhrase($this->request);

            } elseif ($this->request['type'] == 'list') {
                $relevance->analysisByList($this->request);
            }

            $relevance->analysis($this->request, $this->userId, $this->historyId);

        } catch (\Exception $exception) {
            //  игнорируем ошибку: "packets out of order" и другие ошибки бд
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
