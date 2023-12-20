<?php

namespace App\Jobs\Monitoring;

use App\Common;
use App\MonitoringCompetitor;
use App\MonitoringHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MonitoringWaitResultsQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $hash;
    protected int $target;
    protected $project;
    protected $record;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($hash, $target, $project, $record)
    {
        $this->hash = $hash;
        $this->target = $target;
        $this->project = $project;
        $this->record = $record;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $results = MonitoringHelper::where('hash', $this->hash)->get();

        if (count($results) === $this->target) {
            $res = [];
            foreach ($results as $result) {
                $res = array_merge_recursive($res, json_decode($result->result, true));
            }

            $competitors = MonitoringCompetitor::where('monitoring_project_id', $this->project['id'])->pluck('url')->toArray();
            array_unshift($competitors, $this->project['url']);

            $response = [];
            foreach ($res as $date => $queries) {
                foreach ($queries as $lrs) {
                    foreach ($lrs as $positions) {
                        if (count($positions) === 0) {
                            continue;
                        }
                        foreach ($competitors as $competitor) {
                            foreach ($positions as $keyPos => $result) {
                                $url = Common::domainFilter(parse_url($result['url'])['host']);
                                if ($competitor === $url) {
                                    $response[$date][$competitor]['positions'][] = $result['position'];
                                    continue 2;
                                } else if (array_key_last($positions) === $keyPos) {
                                    $response[$date][$competitor]['positions'][] = 101;
                                }
                            }
                        }
                    }
                }
            }

            foreach ($response as $date => $result) {
                foreach ($result as $domain => $data) {
                    $response[$date][$domain]['avg'] = round(array_sum($data['positions']) / count($data['positions']), 2);
                    $response[$date][$domain]['top_3'] = Common::percentHitIn(3, $data['positions'], true);
                    $response[$date][$domain]['top_10'] = Common::percentHitIn(10, $data['positions'], true);
                    $response[$date][$domain]['top_100'] = Common::percentHitIn(100, $data['positions'], true);
                }
            }

            $this->record->update([
                'result' => json_encode($response, JSON_INVALID_UTF8_IGNORE),
                'state' => 'ready',
            ]);

            MonitoringHelper::where('hash', $this->hash)->delete();
        } else {
            MonitoringWaitResultsQueue::dispatch($this->hash, $this->target, $this->project, $this->record)->delay(now()->addSeconds(10));
        }

    }

    public function failed()
    {
        $this->record->update([
            'result' => "",
            'state' => 'fail',
        ]);
    }
}
