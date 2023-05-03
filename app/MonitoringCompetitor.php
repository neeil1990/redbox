<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoringCompetitor extends Model
{
    protected $fillable = ['url'];

    public static function getCompetitors(array $request): string
    {
        $project = MonitoringProject::findOrFail($request['projectId']);
        $engines = isset($request['region'])
            ? MonitoringSearchengine::where('id', '=', $request['region'])->get(['engine', 'lr', 'id'])->toArray()
            : MonitoringSearchengine::where('monitoring_project_id', $project->id)->get(['engine', 'lr', 'id'])->toArray();

        $words = MonitoringKeyword::where('monitoring_project_id', $project->id)->get(['query'])->toArray();
        $words = array_chunk($words, 100);
        $competitors = [];

        foreach ($engines as $engine) {
            $start = microtime(true);
            foreach ($words as $keywords) {
                $results = DB::table('search_indices')
                    ->where('lr', '=', $engine['lr'])
                    ->whereIn('query', $keywords)
                    ->where('position', '<=', 10)
                    ->orderBy('id', 'desc')
                    ->limit(count($keywords) * 10)
                    ->get(['query', 'url'])
                    ->toArray();

                foreach ($results as $result) {
                    $host = parse_url(Common::domainFilter($result->url))['host'];
                    if (isset($request['targetDomain'])) {
                        if ($host === $request['targetDomain']) {
                            $competitors[$host]['urls'][$result->query][$engine['engine']][] = [$engine['location']['name'] => Common::domainFilter($result->url)];
                        }
                    } else {
                        $competitors[$host]['urls'][$engine['engine']][$result->query][] = [$engine['location']['name'] => Common::domainFilter($result->url)];
                    }
                }
            }
            Log::debug(microtime(true) - $start);
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

        return json_encode($competitors, JSON_INVALID_UTF8_IGNORE);
    }

    public static function calculateStatistics(array $request): array
    {
        $competitors = $request['competitors'];
        $keywords = $request['keywords'];
        $countKeyWords = count($keywords);
        $engine = MonitoringSearchengine::where('id', '=', $request['region'])->first(['lr'])->toArray();

        $visibilityArray = [];
        foreach ($keywords as $keyword) {
            foreach ($competitors as $competitor) {
                $visibilityArray[$keyword][$competitor] = 0;
            }
        }

        $records = SearchIndex::whereIn('query', $keywords)
            ->where('lr', $engine['lr'])
            ->orderBy('id', 'desc')
            ->limit($countKeyWords * 100)
            ->get(['url', 'position', 'created_at', 'query'])
            ->toArray();

        foreach ($records as $record) {
            try {
                $url = Common::domainFilter(parse_url($record['url'])['host']);
                if (in_array($url, $competitors) && $visibilityArray[$record['query']][$url] === 0) {
                    $visibilityArray[$record['query']][$url] = $record['position'];
                }
            } catch (\Throwable $e) {
            }
        }


        $competitorStatistics = [];
        foreach ($visibilityArray as $query => $positions) {
            foreach ($competitors as $competitor) {
                $competitorStatistics[$competitor]['positions'][$query] = $positions[$competitor] === 0 ? 101 : $positions[$competitor];
            }
        }

        foreach ($competitorStatistics as $key => $item) {
            $competitorStatistics[$key]['sum'] = array_sum($item['positions']);
            $competitorStatistics[$key]['top_3'] = Common::percentHitIn(3, $item['positions']);
            $competitorStatistics[$key]['top_10'] = Common::percentHitIn(10, $item['positions']);
            $competitorStatistics[$key]['top_100'] = Common::percentHitIn(100, $item['positions']);
        }

        return [
            'visibility' => $visibilityArray,
            'statistics' => $competitorStatistics,
        ];
    }
}
