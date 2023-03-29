<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
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

        $keywords = MonitoringKeyword::where('monitoring_project_id', $project->id)->pluck('query', 'id')->toArray();
        $competitors = [];

        foreach ($engines as $engine) {
            foreach ($keywords as $keywordId => $keyword) {
                $date = MonitoringPosition::where('monitoring_searchengine_id', $engine['id'])
                    ->where('monitoring_keyword_id', $keywordId)
                    ->latest()
                    ->first(['created_at']);

                if (isset($date)) {
                    $results = SearchIndex::where('lr', '=', $engine['lr'])
                        ->where('query', $keyword)
                        ->where('position', '<=', 10)
                        ->where('created_at', '<=', $date->created_at)
                        ->pluck('query', 'url');

                    foreach ($results as $url => $query) {
                        $host = parse_url(Common::domainFilter($url))['host'];
                        if (isset($request['targetDomain'])) {
                            if ($host === $request['targetDomain']) {
                                $competitors[$host]['urls'][$query][$engine['engine']][] = [$engine['lr'] => Common::domainFilter($url)];
                            }
                        } else {
                            $competitors[$host]['urls'][$engine['engine']][$query][] = Common::domainFilter($url);
                        }
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
            $count = 0;
            foreach ($urls as $inf => $engines) {
                if ($inf !== 'urls') {
                    continue;
                }
                foreach ($engines as $k => $words) {
                    foreach ($words as $k1 => $word) {
                        $count += count($word);
                        $competitors[$key][$inf][$k][$k1] = $word;
                    }
                }
            }

            $competitors[$key]['visibility'] = $count;
        }

        return json_encode($competitors, JSON_INVALID_UTF8_IGNORE);
    }
}
