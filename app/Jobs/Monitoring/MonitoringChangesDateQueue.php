<?php

namespace App\Jobs\Monitoring;

use App\Common;
use App\MonitoringCompetitor;
use App\MonitoringKeyword;
use App\MonitoringProject;
use App\MonitoringSearchengine;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoringChangesDateQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $record;

    protected array $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($record, $request)
    {
        $this->record = $record;
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->record->update([
                'state' => 'in process',
            ]);

            $project = MonitoringProject::where('id', $this->record['monitoring_project_id'])->first(['id', 'url']);
            $lr = MonitoringSearchengine::where('id', '=', $this->request['region'])->pluck('lr')->toArray()[0];

            $words = MonitoringKeyword::where('monitoring_project_id', $project['id'])->get(['query'])->toArray();
            $items = array_chunk(array_column($words, 'query'), 10);

            $range = explode(' - ', $this->request['dateRange']);
            $period = CarbonPeriod::create($range[0], $range[1]);
            $dates = [];
            foreach ($period as $date) {
                $dates[] = $date->format('Y-m-d');
            }

            $hash = md5(microtime(true));
            $totalJobs = 0;
            foreach ($dates as $date) {
                foreach ($items as $keywords) {
                    MonitoringHelperQueue::dispatch($date, $lr, $keywords, $hash)->onQueue('monitoring_helper');
                    $totalJobs++;
                }
            }

            MonitoringWaitResultsQueue::dispatch($hash, $totalJobs, $project, $this->record)->onQueue('monitoring_wait');
        } catch (\Throwable $e) {
            $this->record->update([
                'result' => "",
                'state' => 'fail',
            ]);
        }
    }
}
