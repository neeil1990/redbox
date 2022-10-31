<?php

namespace App\Jobs;

use App\Classes\Position\PositionStore;
use App\MonitoringKeyword;
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
    protected $engine;

    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MonitoringKeyword $model, MonitoringSearchengine $engine)
    {
        $this->model = $model;
        $this->engine = $engine;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getParams()
    {
        $en = $this->engine;
        return $en->location->name . ' [' . $en->lr . ']';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $store = new PositionStore(false);
        $store->saveByQuery($this->model, $this->engine);

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
