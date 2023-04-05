<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringCompetitor extends Model
{
    protected $fillable = ['url'];

    public static function getCompetitors(array $request): string
    {
        $project = MonitoringProject::findOrFail($request['projectId']);
        $engines = isset($request['region'])
            ? MonitoringSearchengine::where('id', '=', $request['region'])->get(['engine', 'lr', 'id'])->toArray()
            : MonitoringSearchengine::where('monitoring_project_id', $project->id)->get(['engine', 'lr', 'id'])->toArray();

        $keywords = MonitoringKeyword::where('monitoring_project_id', $project->id)->pluck('query')->toArray();
        $competitors = [];

        foreach ($engines as $engine) {
            foreach ($keywords as $keyword) {
                $results = SearchIndex::where('lr', '=', $engine['lr'])
                    ->where('query', $keyword)
                    ->where('position', '<=', 10)
                    ->orderBy('created_at', 'asc')
                    ->limit(10)
                    ->pluck('query', 'url');

                foreach ($results as $url => $query) {
                    $host = parse_url(Common::domainFilter($url))['host'];
                    if (isset($request['targetDomain'])) {
                        if ($host === $request['targetDomain']) {
                            $competitors[$host]['urls'][$query][$engine['engine']][] = [$engine['location']['name'] => Common::domainFilter($url)];
                        }
                    } else {
                        $competitors[$host]['urls'][$engine['engine']][$query][] = [$engine['location']['name'] => Common::domainFilter($url)];
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

        return json_encode($competitors, JSON_INVALID_UTF8_IGNORE);
    }

    public static function calculateStatistics($keywords, $competitors, $engine): array
    {
        $visibilityArray = [];
        $array = [];
        foreach ($keywords as $keyword) {
            foreach ($competitors as $competitor) {
                $visibilityArray[$keyword['query']][$competitor] = 0;
            }
        }

        foreach ($keywords as $keyword) {
            $records = SearchIndex::where('query', $keyword['query'])
                ->where('lr', $engine['lr'])
                ->latest('created_at')
                ->take(100)
                ->get(['url', 'position', 'created_at', 'query'])
                ->toArray();

            foreach ($records as $record) {
                $url = Common::domainFilter(parse_url($record['url'])['host']);
                if (in_array($url, $competitors) && $visibilityArray[$keyword['query']][$url] === 0) {
                    $visibilityArray[$keyword['query']][$url] = $record['position'];
                }
            }

            foreach ($competitors as $competitor) {
                foreach ($records as $key => $record) {
                    $url = Common::domainFilter(parse_url($record['url'])['host']);
                    if ($url === $competitor) {
                        $array[$competitor]['positions'][$keyword['query']] = $record['position'];

                        continue 2;
                    } else if (array_key_last($records) === $key) {
                        $array[$competitor]['positions'][$keyword['query']] = 101;
                    }
                }
            }
        }

        foreach ($array as $key => $item) {
            $array[$key]['avg'] = round(array_sum($item['positions']) / count($keywords), 2);
            $array[$key]['top_3'] = Common::percentHitIn(3, $item['positions']);
            $array[$key]['top_10'] = Common::percentHitIn(10, $item['positions']);
            $array[$key]['top_100'] = Common::percentHitIn(100, $item['positions']);
        }

        return [
            'visibility' => $visibilityArray,
            'statistics' => $array
        ];
    }
}
