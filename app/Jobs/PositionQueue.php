<?php

namespace App\Jobs;

use Exception;
use App\Classes\Position\PositionStore;
use App\MonitoringKeyword;
use App\MonitoringStat;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PositionQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;

    public $timeout = 0;

    public $tries = 3;

    public $retryAfter = 60;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MonitoringKeyword $keyword)
    {
        $this->model = $keyword;
    }

    /**
     *  Get current model
     *
     * @return MonitoringKeyword
     */
    public function getModel()
    {
        return $this->model;
    }

    public function getParams()
    {
        return $this->model->project->searchengines->implode('lr', ', ');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $store = new PositionStore(true);
        $store->saveByQuery($this->model);

        MonitoringStat::create([
            'queue' => $this->job->getQueue(),
            'queue_id' => $this->job->getJobId(),
            'model_class' => get_class($this->model),
            'model_id' => $this->model->id,
            'errors' => false,
            'msg' => null,
        ]);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        MonitoringStat::create([
            'queue' => $this->job->getQueue(),
            'queue_id' => $this->job->getJobId(),
            'model_class' => get_class($this->model),
            'model_id' => $this->model->id,
            'errors' => true,
            'msg' => $exception->getMessage(),
        ]);
    }
}
