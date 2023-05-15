<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoringCompetitor extends Model
{
    protected $fillable = ['url'];

    public static function getCompetitors(array $request, $targetId = false): ?string
    {
        $project = MonitoringProject::findOrFail($request['projectId']);
        $words = MonitoringKeyword::where('monitoring_project_id', $request['projectId'])->get(['query'])->toArray();
        $words = array_chunk($words, 100);
        $competitors = [];

        if ($request['region'] == '') {
            $days = MonitoringProject::getLastDates($project);
        } else {
            $days = MonitoringProject::getLastDate($project, $request['region']);
        }

        foreach ($days as $day) {
//            $diffInDays = Carbon::parse($day['dateOnly'])->diffInDays(Carbon::now());
//            if ($diffInDays > 15) {
//                continue;
//            }

            foreach ($words as $keywords) {
                $start = microtime(true);
                $results = DB::table(DB::raw('search_indices use index(search_indices_query_index, search_indices_lr_index, search_indices_position_index)'))
                    ->where('search_indices.lr', $day['engine']['lr'])
                    ->where('search_indices.position', '<=', 10)
                    ->whereDate('search_indices.created_at', $day['dateOnly'])
                    ->whereIn('search_indices.query', $keywords)
                    ->orderBy('search_indices.id', 'desc')
                    ->get()
                    ->toArray();

                Log::debug('microtime', [microtime(true) - $start]);

                foreach ($results as $result) {
                    $host = parse_url(Common::domainFilter($result->url))['host'];
                    if (isset($request['targetDomain'])) {
                        if ($host === $request['targetDomain']) {
                            $competitors[$host]['urls'][$result->query][$day['engine']['engine']][] = [$day['engine']['location']['name'] => Common::domainFilter($result->url)];
                        }
                    } else {
                        $competitors[$host]['urls'][$day['engine']['engine']][$result->query][] = [$day['engine']['location']['name'] => Common::domainFilter($result->url)];
                    }
                }
            }
        }

        foreach ($project->competitors as $competitor) {
            $url = Common::domainFilter($competitor->url);

            if (array_key_exists($url, $competitors)) {
                $competitors[$url]['competitor'] = true;
            }
        }

        if (array_key_exists($project->url, $competitors)) {
            $competitors[$project->url]['mainPage'] = true;
        }

        foreach ($competitors as $key => $urls) {
            $total = 0;
            $yandex = [];
            $google = [];
            foreach ($urls as $inf => $engines) {
                if ($inf !== 'urls') {
                    continue;
                }
                foreach ($engines as $engine => $words) {
                    foreach ($words as $k1 => $word) {
                        if ($engine === 'yandex') {
                            foreach ($word as $info) {
                                $region = array_key_first($info);
                                if (isset($yandex[$region])) {
                                    $yandex[$region] += 1;
                                } else {
                                    $yandex[$region] = 1;
                                }
                            }
                        } else if ($engine === 'google') {
                            foreach ($word as $info) {
                                $region = array_key_first($info);
                                if (isset($google[$region])) {
                                    $google[$region] += 1;
                                } else {
                                    $google[$region] = 1;
                                }
                            }
                        }
                        $total += count($word);
                        $competitors[$key][$inf][$engine][$k1] = $word;
                    }
                }
            }

            $competitors[$key]['visibility'] = $total;
            $competitors[$key]['visibilityYandex'] = $yandex;
            $competitors[$key]['visibilityGoogle'] = $google;
        }

        if ($targetId !== false) {
            MonitoringCompetitorsResult::where('id', $targetId)->update([
                'result' => Common::compressArray($competitors, JSON_INVALID_UTF8_IGNORE),
                'state' => 'ready'
            ]);
        }

        return json_encode($competitors, JSON_INVALID_UTF8_IGNORE);
    }

    public static function calculateStatistics(array $request): array
    {
        $competitorStatistics = [];
        $visibilityArray = [];
        $lastDate = MonitoringProject::getLastDateByWords($request['keywords'], $request['region']);

        $queries = array_column($request['keywords'], 'query');
        foreach ($queries as $keyword) {
            foreach ($request['competitors'] as $competitor) {
                $visibilityArray[$keyword][$competitor] = 0;
            }
        }

        if (isset($lastDate)) {
            $start = microtime(true);
            $records = DB::table(DB::raw('search_indices use index(search_indices_query_index, search_indices_lr_index, search_indices_position_index)'))
                ->where('search_indices.lr', $lastDate['engine']['lr'])
                ->whereDate('search_indices.created_at', $lastDate['dateOnly'])
                ->whereIn('search_indices.query', $queries)
                ->orderBy('search_indices.id', 'desc')
                ->limit(count($request['keywords']) * 100)
                ->get(['search_indices.url', 'search_indices.position', 'search_indices.created_at', 'search_indices.query'])
                ->toArray();

            Log::debug('microtime', [microtime(true) - $start]);
            foreach ($records as $record) {
                try {
                    $url = Common::domainFilter(parse_url($record->url)['host']);
                    if (in_array($url, $request['competitors']) && $visibilityArray[$record->query][$url] === 0) {
                        $visibilityArray[$record->query][$url] = $record->position;
                    }
                } catch (\Throwable $e) {
                }
            }

            foreach ($visibilityArray as $query => $positions) {
                foreach ($request['competitors'] as $competitor) {
                    $competitorStatistics[$competitor]['positions'][$query] = $positions[$competitor] === 0 ? 101 : $positions[$competitor];
                }
            }

            foreach ($competitorStatistics as $key => $item) {
                $competitorStatistics[$key]['sum'] = array_sum($item['positions']);
                $competitorStatistics[$key]['top_3'] = Common::percentHitIn(3, $item['positions']);
                $competitorStatistics[$key]['top_10'] = Common::percentHitIn(10, $item['positions']);
                $competitorStatistics[$key]['top_100'] = Common::percentHitIn(100, $item['positions']);
            }
        }

        return [
            'visibility' => $visibilityArray,
            'statistics' => $competitorStatistics,
        ];
    }
}
