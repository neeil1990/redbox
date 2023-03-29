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
            ? MonitoringSearchengine::where('id', '=', $request['region'])->get(['lr', 'engine'])
            : $project->searchengines;

        $competitors = [];

        foreach ($engines as $searchengine) {
            foreach ($project->keywords as $keyword) {
                $results = SearchIndex::where('lr', '=', $searchengine->lr)
                    ->where('query', '=', $keyword->query)
                    ->where('position', '<=', 10)
                    ->latest()
                    ->pluck('query', 'url');

                foreach ($results as $url => $query) {
                    $host = parse_url(Common::domainFilter($url))['host'];
                    if (isset($request['targetDomain'])) {
                        if ($host === $request['targetDomain']) {
                            $competitors[$host]['urls'][$keyword->query][$searchengine->engine][] = Common::domainFilter($url);
                        }
                    } else {
                        $competitors[$host]['urls'][$searchengine->engine][$keyword->query][] = Common::domainFilter($url);
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
                        $word = array_unique($word);
                        $count += count($word);
                        $competitors[$key][$inf][$k][$k1] = $word;
                    }
                }
            }

            $competitors[$key]['visibility'] = $count;
        }

        return json_encode($competitors);
    }
}
