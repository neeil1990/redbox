<?php

namespace App\Jobs;

use App\Classes\Position\PositionStore;
use App\MonitoringKeyword;
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $store = new PositionStore($this->model, false);
        $store->save();
    }
}
