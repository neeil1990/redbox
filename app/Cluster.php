<?php

namespace App;

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

    public function __construct(array $request)
    {
        $this->count = $request['count'];
        $this->region = $request['region'];
        $this->clusteringLevel = $request['clustering_level'] == 5 ? 0.5 : 0.7;
        $this->engineVersion = $request['engine_version'];

        $this->phrases = array_unique(array_diff(explode("\n", str_replace("\r", "", $request['phrases'])), []));
        $this->countPhrases = count($this->phrases);
    }

    public function startAnalysis()
    {
        try {
            $this->setSites();
            $this->searchClusters();
            $this->calculateClustersInfo();
        } catch (\Throwable $e) {
//            Log::debug('cluster error', [
//                $e->getMessage(),
//                $e->getLine(),
//                $e->getFile()
//            ]);
            dd([
                $e->getMessage(),
                $e->getLine(),
                $e->getFile()
            ]);
        }
    }

    protected function setSites()
    {
        $xml = new SimplifiedXmlFacade($this->region, $this->count);
        foreach ($this->phrases as $phrase) {
            $xml->setQuery($phrase);
            $this->sites[$phrase]['sites'] = $xml->getXMLResponse();
        }
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
                if (isset($clusters[$phrase])) {
                    foreach ($clusters[$phrase] as $elems) {
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
            $this->clusters[$key]['finallyResult'] = $merge;
        }

        $this->searchForms();
    }

    protected function searchForms()
    {
        foreach ($this->clusters as $key => $cluster) {
            $riwerResponse = $this->basedRiverRequest($cluster);

            foreach ($cluster as $phrase => $sites) {
                if ($phrase !== 'finallyResult') {
                    $options = $this->phraseOptions($phrase);
                    foreach ($riwerResponse['content']['includingPhrases']['items'] as $item) {
                        if ($item['phrase'] == $phrase) {
                            $this->clusters[$key][$phrase]['group'] = $item['phrase'];
                            $this->clusters[$key][$phrase]['based'] = $item['number'];
                            $this->clusters[$key][$phrase]['searchType'] = 'equivalent';
                        } else if (in_array($item['phrase'], $options)) {
                            $this->clusters[$key][$phrase]['group'] = $item['phrase'];
                            $this->clusters[$key][$phrase]['based'] = $item['number'];
                            $this->clusters[$key][$phrase]['searchType'] = 'similar';
                        }
                    }
                }
            }

        }
//        dd($this->clusters);
//        foreach ($phrases as $phrase) {
//            $based[] = $phrase;
//            $phrased[] = '"' . $phrase . '"';
//            $target[] = "!" . implode(' !', explode(' ', $phrase));
//        }
//
//        $based = $riwerPath . '(' . implode(' | ', $based) . ')';
//        $phrased = $riwerPath . '(' . implode(' | ', $phrased) . ')';
//        $target = $riwerPath . '(' . implode(' | ', $target) . ')';
//
//        $based = str_replace(' ', '%20', $based);
//        dump($based);

        $this->setResult($this->clusters);
    }

    /**
     * @param $cluster
     * @return array
     */
    protected function basedRiverRequest($cluster): array
    {
        $based = [];
        foreach ($cluster as $phrase => $sites) {
            if ($phrase !== 'finallyResult') {
                $based[] = $phrase;
            }
        }
        if (count($based) > 1) {
            $based = $this->xmlRiwerPath . '(' . implode(' | ', $based) . ')';
        } else {
            $based = $this->xmlRiwerPath . implode(' | ', $based);
        }
        $based = str_replace(' ', '%20', $based);

        Log::debug('based', [$based]);
        return json_decode(file_get_contents($based), true);
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
    protected function setResult(array $result)
    {
        $this->result = collect($result)->sortByDesc(function ($item, $key) {
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
