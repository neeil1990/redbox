<?php

namespace App;

use App\Classes\Xml\SimplifiedXmlFacade;

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
                    $this->clusters[$phrase][$phrase2] = $sites2['sites'];
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
                $this->clusters[$phrase][$itemPhrase] = $elems[0];
            }
        }
    }

    protected function calculateClustersInfo()
    {
        foreach ($this->clusters as $key => $phrases) {
            $merge = [];
            foreach ($phrases as $phrase => $sites) {
                $merge = array_merge($merge, $sites);
            }
            $merge = array_count_values($merge);
            arsort($merge);
            $this->clusters[$key]['finallyResult'] = $merge;
        }

        $this->setResult($this->clusters);
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
