<?php

namespace App;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Jobs\ClusterQueue;
use Illuminate\Support\Facades\Log;

class Cluster
{
    protected $count;

    protected $region;

    protected $phrases;

    protected $clusteringLevel;

    protected $countPhrases;

    protected $sites;

    protected $result;

    protected $clusters = [];

    protected $engineVersion;

    protected $searchPhrases;

    protected $searchTarget;

    protected $progress;

    public function __construct(array $request)
    {
        $this->count = $request['count'];
        $this->region = $request['region'];
        $this->clusteringLevel = $request['clusteringLevel'] == 5 ? 0.5 : 0.7;
        $this->engineVersion = $request['engineVersion'];
        $this->searchPhrases = filter_var($request['searchPhrases'], FILTER_VALIDATE_BOOLEAN);
        $this->searchTarget = filter_var($request['searchTarget'], FILTER_VALIDATE_BOOLEAN);

        $this->phrases = array_unique(array_diff(explode("\n", str_replace("\r", "", $request['phrases'])), []));
        $this->countPhrases = count($this->phrases);

        $this->progress = ClusterProgress::where('id', '=', $request['progressId'])->first();
    }

    public function startAnalysis()
    {
        try {
            $this->setSites();
            $this->searchClusters();
            $this->calculateClustersInfo();
            $this->wordStats();
            $this->searchGroupName();
            $this->setResult($this->clusters);

        } catch (\Throwable $e) {
            Log::debug('cluster error', [
                $e->getMessage(),
                $e->getLine(),
                $e->getFile()
            ]);
            $this->progress->delete();
        }
    }

    protected function setSites()
    {
        $xml = new SimplifiedXmlFacade($this->region, $this->count);
        foreach ($this->phrases as $phrase) {
            $xml->setQuery($phrase);
            $this->sites[$phrase]['sites'] = $xml->getXMLResponse();
        }

        ksort($this->sites);
    }

    protected function searchClusters()
    {
        if ($this->engineVersion === 'old') {
            $this->searchClustersEngineV1();
        } else {
            $this->searchClustersEngineV2();
        }
    }

    protected function searchClustersEngineV1()
    {
        $minimum = $this->count * $this->clusteringLevel;
        $willClustered = [];

        foreach ($this->sites as $phrase => $sites) {
            foreach ($this->sites as $phrase2 => $sites2) {
                if (isset($willClustered[$phrase2])) {
                    continue;
                } elseif (count(array_intersect($sites['sites'], $sites2['sites'])) >= $minimum) {
                    $this->clusters[$phrase][$phrase2]['sites'] = $sites2['sites'];
                    $willClustered[$phrase2] = true;
                }
            }
        }
    }

    protected function searchClustersEngineV2()
    {
        $minimum = $this->count * $this->clusteringLevel;
        $willClustered = [];
        $clusters = [];
        foreach ($this->sites as $phrase => $sites) {
            foreach ($this->sites as $phrase2 => $sites2) {
                if (isset($willClustered[$phrase2])) {
                    continue;
                }
                if (isset($clusters[$phrase2])) {
                    foreach ($clusters[$phrase2] as $elems) {
                        foreach ($elems as $elem) {
                            if (count(array_intersect($elem, $sites2['sites'])) >= $minimum) {
                                $clusters[$phrase][$phrase2][] = $sites2['sites'];
                                $willClustered[$phrase2] = true;
                                break 2;
                            }
                        }
                    }
                } else {
                    if (count(array_intersect($sites['sites'], $sites2['sites'])) >= $minimum) {
                        $clusters[$phrase][$phrase2][] = $sites2['sites'];
                        $willClustered[$phrase2] = true;
                    }
                }
            }
        }

        foreach ($clusters as $mainPhrase => $items) {
            if (count($items) > 1) {
                continue;
            }
            foreach ($clusters as $mainPhrase2 => $items2) {
                if ($mainPhrase === $mainPhrase2) {
                    continue;
                }
                foreach ($items2 as $item) {
                    if (count(array_intersect($items[$mainPhrase][0], $item[0])) >= $minimum) {
                        $jayParsedAry[$mainPhrase2][$mainPhrase] = $items[$mainPhrase];
                        unset($jayParsedAry[$mainPhrase]);
                    }
                }

            }
        }

        foreach ($clusters as $phrase => $item) {
            foreach ($item as $itemPhrase => $elems) {
                $this->clusters[$phrase][$itemPhrase]['sites'] = $elems[0];
            }
        }
    }

    protected function calculateClustersInfo()
    {
        foreach ($this->clusters as $key => $phrases) {
            $merge = [];
            foreach ($phrases as $phrase => $sites) {
                $merge = array_merge($merge, $sites['sites']);
            }
            $merge = array_count_values($merge);
            arsort($merge);
            $this->clusters[$key]['finallyResult']['sites'] = $merge;
        }
    }

    protected function wordStats()
    {
        $this->progress->total = $this->calculateCountRequests();
        $this->progress->save();
        $percent = 90 / $this->progress->total;

        foreach ($this->clusters as $key => $cluster) {
            foreach ($cluster as $phrase => $sites) {
                if ($phrase !== 'finallyResult') {
                    if ($this->searchPhrases) {
                        ClusterQueue::dispatch(
                            $this->region,
                            $this->progress->id,
                            $percent,
                            '"' . $phrase . '"',
                            $key,
                            $phrase,
                            'phrased'
                        )->onQueue('cluster_high');
                    }

                    if ($this->searchTarget) {
                        ClusterQueue::dispatch(
                            $this->region,
                            $this->progress->id,
                            $percent,
                            '"!' . implode(' !', explode(' ', $phrase)) . '"',
                            $key,
                            $phrase,
                            'target'
                        )->onQueue('cluster_high');
                    }

                    ClusterQueue::dispatch(
                        $this->region,
                        $this->progress->id,
                        $percent,
                        $phrase,
                        $key,
                        $phrase,
                        'based'
                    )->onQueue('cluster_high');
                }
            }
        }

        $this->waitRiverResponses();
        $this->setRiverResults();
    }

    protected function waitRiverResponses()
    {
        $this->progress = ClusterProgress::where('id', '=', $this->progress->id)->first();

        while ($this->progress->total !== $this->progress->success) {
            Log::debug('сплю 5 секунд', [$this->progress->total, $this->progress->success]);
            sleep(5);
            $this->progress = ClusterProgress::where('id', '=', $this->progress->id)->first();
        }
    }

    protected function setRiverResults()
    {
        $array = json_decode($this->progress->array, true);
        foreach ($this->clusters as $key => $cluster) {
            foreach ($cluster as $phrase => $sites) {
                if ($phrase !== 'finallyResult') {
                    if ($this->searchPhrases) {
                        $this->clusters[$key][$phrase]['phrased'] = $array[$key][$phrase]['phrased'];
                    }

                    if ($this->searchTarget) {
                        $this->clusters[$key][$phrase]['target'] = $array[$key][$phrase]['target'];
                    }

                    Log::debug('a', [$array[$key][$phrase]]);
                    $this->clusters[$key][$phrase]['based'] = $array[$key][$phrase]['based'];
                    if ($array[$key][$phrase]['based']['phrase'] !== $phrase) {
                        $this->clusters[$key][$phrase]['basedNormal'] = $array[$key][$phrase]['based']['phrase'];
                    }
                }
            }
        }

    }

    /**
     * @return int
     */
    protected function calculateCountRequests(): int
    {
        $first = $this->searchPhrases ? 1 : 0;
        $second = $this->searchTarget ? 1 : 0;

        return $this->countPhrases * (1 + $first + $second);
    }

    protected function searchGroupName()
    {
        foreach ($this->clusters as $key => $cluster) {
            $maxRepeatPhrase = 0;
            $groupName = '';
            foreach ($cluster as $phrase => $info) {
                if ($phrase !== 'finallyResult') {
                    if ($info['based']['number'] > $maxRepeatPhrase) {
                        $maxRepeatPhrase = $info['based']['number'];
                        $groupName = $info['based']['phrase'];
                    }
                }
            }
            $this->clusters[$key]['finallyResult']['groupName'] = $groupName;
        }
    }

    /**
     * @param array $results
     * @return void
     */
    protected function setResult(array $results)
    {
        $this->result = collect($results)->sortByDesc(function ($item, $key) {
            return count($item);
        })->values()->all();
    }

    /**
     * @return array
     */
    protected function getResult(): array
    {
        return $this->result;
    }

    /**
     * @return array
     */
    public function getAnalysisResult(): array
    {
        return $this->getResult();
    }

}
