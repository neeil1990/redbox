<?php

namespace App\Jobs;

use App\Common;
use App\MonitoringChangesDate;
use App\MonitoringCompetitor;
use App\MonitoringKeyword;
use App\MonitoringProject;
use App\MonitoringSearchengine;
use Carbon\CarbonPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class MonitoringChangesDateQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $record;

    protected array $request;

    public function attempts()
    {
    }

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
            $competitors = MonitoringCompetitor::where('monitoring_project_id', $project['id'])->pluck('url')->toArray();
            array_unshift($competitors, $project['url']);
            $lr = MonitoringSearchengine::where('id', '=', $this->request['region'])->pluck('lr')->toArray()[0];

            $words = MonitoringKeyword::where('monitoring_project_id', $project['id'])->get(['query'])->toArray();
            $items = array_chunk(array_column($words, 'query'), 10);

            $range = explode(' - ', $this->request['dateRange']);
            $period = CarbonPeriod::create($range[0], $range[1]);
            $dates = [];
            foreach ($period as $date) {
                $dates[] = $date->format('Y-m-d');
            }

            $records = [];
            foreach ($dates as $date) {
                foreach ($items as $keywords) {
                    $results = DB::table(DB::raw('search_indices use index(search_indices_query_index, search_indices_lr_index, search_indices_position_index)'))
                        ->whereBetween('created_at', [
                            date('Y-m-d H:i:s', strtotime($date . ' 00:00:00')),
                            date('Y-m-d H:i:s', strtotime($date . ' 23:59:59')),
                        ])
                        ->where('lr', $lr)
                        ->whereIn('query', $keywords)
                        ->where('position', '<=', 100)
                        ->orderBy('id', 'desc')
                        ->limit(count($keywords) * 100)
                        ->select(DB::raw('url, position, created_at, query'))
                        ->get();

                    if (count($results) === 0) {
                        continue;
                    }

                    foreach ($results as $result) {
                        $records[$date][$result->query][$lr][] = $result;
                    }
                }
            }

            $response = [];
            foreach ($records as $date => $queries) {
                foreach ($queries as $lrs) {
                    foreach ($lrs as $positions) {
                        if (count($positions) === 0) {
                            continue;
                        }
                        foreach ($competitors as $competitor) {
                            foreach ($positions as $keyPos => $result) {
                                $url = Common::domainFilter(parse_url($result->url)['host']);
                                if ($competitor === $url) {
                                    $response[$date][$competitor]['positions'][] = $result->position;
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
        } catch (\Throwable $e) {
            $this->record->update([
                'result' => "",
                'state' => 'fail',
            ]);
        }
    }
}