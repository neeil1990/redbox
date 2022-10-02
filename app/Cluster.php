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

    protected $message;

    protected $result;

    protected $clusters = [];

    public function __construct(array $request)
    {
        $this->count = $request['count'];
        $this->region = $request['region'];
        $this->clusteringLevel = (int)'0.' . $request['clustering_level'];

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
        $minimum = $this->countPhrases * $this->clusteringLevel;
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

    protected function calculateClustersInfo()
    {
        foreach ($this->clusters as $key => $phrases) {
            $merge = [];
            foreach ($phrases as $phrase => $sites) {
                $merge = array_merge($merge, $sites);
            }
            $this->clusters[$key]['finallyResult'] = array_count_values($merge);
        }

        $this->setResult($this->clusters);
        $this->setMessage('success');
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
     * @param string $message
     * @return void
     */
    protected function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    protected function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string[]
     */
    public function getAnalysisResult(): array
    {
        return [
            'message' => $this->getMessage(),
            'result' => $this->getResult()
        ];
    }


}
