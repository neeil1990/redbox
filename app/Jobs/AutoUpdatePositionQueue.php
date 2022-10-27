<?php

namespace App\Jobs;

use App\Classes\Position\PositionStore;
use App\MonitoringSearchengine;
use App\MonitoringStat;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AutoUpdatePositionQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;

    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MonitoringSearchengine $model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $store = new PositionStore(false);
        $store->saveBySearchEngines($this->model);

        MonitoringStat::create([
            'queue' => $this->job->getQueue(),
            'queue_id' => $this->job->getJobId(),
            'model_class' => get_class($this->model),
            'model_id' => $this->model->id,
            'errors' => false,
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
        ]);
    }
}
