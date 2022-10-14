<?php

namespace App;

use App\Classes\Xml\RiverFacade;
use App\Classes\Xml\SimplifiedXmlFacade;
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

    protected $xmlRiwerPath = 'https://xmlriver.com/wordstat/json?user=6602&key=8c0d8e659c4ba2240e791fb3e6b4f172556be01f&query=';

    protected $searchPhrases;

    protected $searchTarget;


    public function __construct(array $request)
    {
        $this->count = $request['count'];
        $this->region = $request['region'];
        $this->clusteringLevel = $request['clustering_level'] == 5 ? 0.5 : 0.7;
        $this->engineVersion = $request['engine_version'];
        $this->searchPhrases = isset($request['searchPhrases']);
        $this->searchTarget = isset($request['searchTarget']);

        $this->phrases = array_unique(array_diff(explode("\n", str_replace("\r", "", $request['phrases'])), []));
        $this->countPhrases = count($this->phrases);
    }

    public function startAnalysis()
    {
//        try {
        $this->setSites();
        $this->searchClusters();
        $this->calculateClustersInfo();
        $this->wordStats();
        $this->searchGroupName();
        $this->setResult($this->clusters);

//        } catch (\Throwable $e) {
//            Log::debug('cluster error', [
//                $e->getMessage(),
//                $e->getLine(),
//                $e->getFile()
//            ]);
//            dd([
//                $e->getMessage(),
//                $e->getLine(),
//                $e->getFile()
//            ]);
//        }
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
        $river = new RiverFacade($this->region);

        foreach ($this->clusters as $key => $cluster) {
            foreach ($cluster as $phrase => $sites) {
                if ($phrase !== 'finallyResult') {
                    if ($this->searchPhrases) {
                        $river->setQuery('"' . $phrase . '"');
                        $this->clusters[$key][$phrase]['phrased'] = $river->riverRequest();
                    }

                    if ($this->searchTarget) {
                        $river->setQuery('"!' . implode(' !', explode(' ', $phrase)) . '"');
                        $this->clusters[$key][$phrase]['target'] = $river->riverRequest();
                    }

                    $river->setQuery($phrase);
                    $response = $river->riverRequest();
                    $this->clusters[$key][$phrase]['based'] = $response;
                    if ($response['phrase'] !== $phrase) {
                        $this->clusters[$key][$phrase]['basedNormal'] = $response['phrase'];
                    }
                }
            }
        }

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
     * @param $string
     * @return array
     */
    protected function riverRequest($string): array
    {
        $url = $this->xmlRiwerPath . $string;
        $url = str_replace(' ', '%20', $url);
        $riwerResponse = [];

        $attempt = 1;
        while (!isset($riwerResponse['content']['includingPhrases']['items']) && $attempt <= 3) {
//            Log::debug('request', [$url]);
            $riwerResponse = json_decode(file_get_contents($url), true);
            $attempt++;
        }

        return [
            'number' => preg_replace('/[^0-9]/', '', $riwerResponse['content']['includingPhrases']['info'][2]),
            'phrase' => str_replace(['"', '!'], "", $string)
        ];
    }

    /**
     * @param $phrase
     * @return array
     */
    protected function phraseOptions($phrase): array
    {
        $result = [$phrase];
        $default = explode(' ', $phrase);

        if (count($default) === 0) {
            return $result;
        }

        if (count($default) === 2) {
            return [
                $phrase,
                implode(' ', array_reverse(explode(' ', $phrase)))
            ];
        }

        foreach ($default as $key => $word) {
            if ($key + 1 >= count($default)) {
                continue;
            }
            $std = $default;
            $tmp = $std[$key];
            $std[$key] = $std[$key + 1];
            $std[$key + 1] = $tmp;

            $result[] = implode(' ', $std);
        }

        return $result;
    }

    /**
     * @param array $result
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
     * @return string[]
     */
    public function getAnalysisResult(): array
    {
        return [
            'result' => $this->getResult()
        ];
    }

}
