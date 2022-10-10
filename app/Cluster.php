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

    private $xmlRiwerPath = 'https://xmlriver.com/wordstat/json?user=6602&key=8c0d8e659c4ba2240e791fb3e6b4f172556be01f&query=';

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
            $riwerResponse = $this->prepareRiverRequest($cluster);

            foreach ($cluster as $phrase => $sites) {
                if ($phrase !== 'finallyResult') {
                    $options = $this->phraseOptions($phrase);
                    $this->setValues($riwerResponse, 'based', $key, $phrase, $options);
                    $this->setValues($riwerResponse, 'target', $key, $phrase, $options);
                    $this->setValues($riwerResponse, 'phrased', $key, $phrase, $options);
                }
            }

        }

        $this->setResult($this->clusters);
    }

    protected function setValues($riwerResponse, $type, $key, $phrase, $options)
    {
        foreach ($riwerResponse[$type] as $item) {
            if ($item['phrase'] === $phrase) {
                if ($type === 'based') {
                    $this->clusters[$key][$phrase]['group'] = $item['phrase'];
                    $this->clusters[$key][$phrase]['based'] = $item['number'];
                    $this->clusters[$key][$phrase]['searchType'] = 'equivalent';
                } elseif ($type === 'phrased') {
                    $this->clusters[$key][$phrase]['phrased'] = $item['number'];
                } elseif ($type === 'target') {
                    $this->clusters[$key][$phrase]['target'] = $item['number'];
                }

            } else if (in_array($item['phrase'], $options)) {
                if ($type === 'based') {
                    $this->clusters[$key][$phrase]['group'] = $item['phrase'];
                    $this->clusters[$key][$phrase]['based'] = $item['number'];
                    $this->clusters[$key][$phrase]['searchType'] = 'similar';
                } elseif ($type === 'phrased') {
                    $this->clusters[$key][$phrase]['phrased'] = $item['number'];
                } elseif ($type === 'target') {
                    $this->clusters[$key][$phrase]['target'] = $item['number'];
                }
            }
        }
    }

    /**
     * @param $cluster
     * @return array
     */
    protected function prepareRiverRequest($cluster): array
    {
        $items = [
            'based' => [],
            'phrased' => [],
            'target' => [],
        ];
        $based = [];
        $phrased = [];
        $target = [];

        foreach ($cluster as $phrase => $sites) {
            if ($phrase !== 'finallyResult') {
                $based[] = $phrase;
                $phrased[] = '"' . $phrase . '"';
                $target[] = '"!' . implode(' !', explode(' ', $phrase)) . '"';

                if ($this->searchPhrases) {
                    $items['phrased'][] = array_merge($items['phrased'], $this->riverRequest($phrased, true));
                    $phrased = [];
                }

                if ($this->searchTarget) {
                    $items['target'][] = array_merge($items['target'], $this->riverRequest($target, true));
                    $target = [];
                }

                if (count($based) % 3 === 0) {
                    $items['based'] = array_merge($items['based'], $this->riverRequest($based));
                    $based = [];
                }
            }
        }

        if (count($based) > 0) {
            $items['based'] = array_merge($items['based'], $this->riverRequest($based));
        }

        return $items;
    }

    /**
     * @param $array
     * @param bool $notBased
     * @return array
     */
    protected function riverRequest($array, bool $notBased = false): array
    {
        if (count($array) > 1) {
            $url = $this->xmlRiwerPath . '(' . implode(' | ', $array) . ')';
        } else {
            $url = $this->xmlRiwerPath . implode(' | ', $array);
        }
        $url = str_replace(' ', '%20', $url);
        $riwerResponse = [];

        $attempt = 1;
        while (!isset($riwerResponse['content']['includingPhrases']['items']) && $attempt <= 3) {
            $riwerResponse = json_decode(file_get_contents($url), true);
            $attempt++;
        }

        if ($notBased) {
            return [
                'number' => preg_replace('/[^0-9]/', '', $riwerResponse['content']['includingPhrases']['info'][2]),
                'phrase' => str_replace(['"', '!'], "", implode(' ', $array))
            ];
        }

        return $riwerResponse['content']['includingPhrases']['items'] ?? [];
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
