<?php

namespace App;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Jobs\Cluster\ClusterQueue;
use App\Jobs\Cluster\WaitClusterAnalyseQueue;
use Illuminate\Support\Facades\Log;

class Cluster
{
    public $count;

    protected $region;

    protected $phrases;

    protected $clusteringLevel;

    protected $countPhrases;

    protected $sites;

    protected $result;

    protected $clusters = [];

    protected $engineVersion;

    protected $searchBase;

    protected $searchPhrases;

    protected $searchTarget;

    protected $searchRelevance;

    protected $searchEngine;

    protected $progress;

    protected $save;

    protected $request;

    protected $newCluster;

    protected $user;

    protected $brutForce;

    protected $xml;

    protected $host;

    protected $mode;

    protected $minimum;

    protected $progressId;

    protected $brutForceCount;


    protected $reductionRatio;

    protected $ignoredWords;

    public $ignoredDomains;

    protected $gainFactor;

    protected $brutForceType;

    protected $wordRatio = [];

    public function __construct(array $request, $user, $default = true)
    {
        $this->ignoredWords = isset($request['ignoredWords']) ? explode("\n", $request['ignoredWords']) : [];
        $this->ignoredDomains = isset($request['ignoredDomains']) ? explode("\n", $request['ignoredDomains']) : [];
        $this->brutForce = filter_var($request['brutForce'], FILTER_VALIDATE_BOOLEAN);
        $this->gainFactor = $request['gainFactor'] ?? 10;
        $this->brutForceCount = $request['brutForceCount'] ?? 1;
        $this->reductionRatio = $request['reductionRatio'] ?? 0.4;
        $this->brutForceType = $request['brutForceType'] ?? 'old';

        if (!isset($request['mode']) || $request['mode'] !== 'professional') {
            $this->count = 40;
            $this->clusteringLevel = 0.6;
        } else {
            $this->count = $request['count'];

            switch ($request['clusteringLevel']) {
                case 'light':
                    $this->clusteringLevel = 0.4;
                    break;
                case 'soft':
                    $this->clusteringLevel = 0.5;
                    break;
                case 'pre-hard':
                    $this->clusteringLevel = 0.6;
                    break;
                case 'hard':
                    $this->clusteringLevel = 0.7;
                    break;
            }
        }
        $this->minimum = $this->count * $this->clusteringLevel;
        $this->mode = $request['mode'];
        $this->engineVersion = $request['engineVersion'];

        if ($default) {
            $this->request = $request;
            $this->searchBase = filter_var($request['searchBase'], FILTER_VALIDATE_BOOLEAN);
            $this->engineVersion = $request['engineVersion'];
            $this->searchEngine = $request['searchEngine'];
            $this->user = $user;
            $this->region = $request['region'];
            $this->searchRelevance = filter_var($request['searchRelevance'], FILTER_VALIDATE_BOOLEAN);
            $this->searchPhrases = filter_var($request['searchPhrases'], FILTER_VALIDATE_BOOLEAN);
            $this->searchTarget = filter_var($request['searchTarget'], FILTER_VALIDATE_BOOLEAN);
            $this->save = filter_var($request['save'], FILTER_VALIDATE_BOOLEAN);
            $this->phrases = array_unique(array_diff(explode("\n", str_replace("\r", "", $request['phrases'])), []));
            $this->countPhrases = count($this->phrases);
            $this->progressId = $request['progressId'];

            $this->xml = new SimplifiedXmlFacade($this->region, 100);

            $this->host = $this->searchRelevance ? parse_url($this->request['domain'])['host'] : $this->request['domain'];
        }
    }

    public function __sleep()
    {
        return [
            'count', 'region', 'phrases', 'clusteringLevel', 'countPhrases', 'gainFactor', 'brutForceType',
            'sites', 'result', 'clusters', 'engineVersion', 'searchBase', 'searchPhrases', 'wordRatio',
            'searchTarget', 'searchRelevance', 'searchEngine', 'progress', 'save', 'request',
            'newCluster', 'user', 'brutForce', 'xml', 'host', 'mode', 'minimum', 'progressId',
            'brutForceCount', 'reductionRatio', 'ignoredWords', 'ignoredDomains'
        ];
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function getProgressTotal(): int
    {
        return $this->countPhrases;
    }

    public function getProgressCurrentCount(): int
    {
        return \App\ClusterQueue::where('progress_id', '=', $this->getProgressId())->count();
    }

    public function getClusters(): array
    {
        return $this->clusters;
    }

    public function getXml(): SimplifiedXmlFacade
    {
        return $this->xml;
    }

    public function getProgressId(): string
    {
        return $this->progressId;
    }

    public function getRegion(): string
    {
        return $this->request['region'];
    }

    public function getSearchPhrases(): bool
    {
        return $this->searchPhrases;
    }

    public function getSearchTarget(): bool
    {
        return $this->searchTarget;
    }

    public function getSearchRelevance(): bool
    {
        return $this->searchRelevance;
    }

    public function getSearchBase(): bool
    {
        return $this->searchBase;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getSearchEngine(): string
    {
        return $this->searchEngine;
    }

    public function startAnalysis()
    {
        foreach ($this->phrases as $key => $phrase) {
            dispatch(new ClusterQueue($this, $key, $phrase))->onQueue('child_cluster');
        }

        dispatch(new WaitClusterAnalyseQueue($this))->onQueue('cluster_wait');
    }

    public function calculate()
    {
        $results = \App\ClusterQueue::where('progress_id', '=', $this->getProgressId())->get();
        $res = [];
        foreach ($results as $result) {
            $res = array_merge_recursive($res, json_decode($result->json, true));
        }

        foreach ($res as $item) {
            $this->sites[array_key_first($item)] = $item[array_key_first($item)];
        }

        $this->searchClusters();
        $this->calculateClustersInfo();
        if ($this->getSearchBase()) {
            $this->searchGroupName();
        }
        $this->setResult($this->clusters);
        $this->saveResult();

        if (isset($this->request['sendMessage']) && filter_var($this->request['sendMessage'], FILTER_VALIDATE_BOOLEAN)) {
            $this->sendNotification();
        }

        \App\ClusterQueue::where('progress_id', '=', $this->getProgressId())->delete();
    }

    protected function markIgnoredDomains()
    {
        foreach ($this->sites as $phrase => $item) {
            $count = 0;
            foreach ($item['sites'] as $key => $site) {
                if ($count < $this->count) {
                    foreach ($this->ignoredDomains as $ignoredDomain) {
                        if (strpos($site, $ignoredDomain)) {
                            $this->sites[$phrase]['mark'][$site] = true;
                            continue 2;
                        }
                    }
                    $this->sites[$phrase]['mark'][$site] = false;
                    $count++;
                } else {
                    break;
                }
            }
        }
    }

    /**
     * @param $sites
     * @return void
     */
    public function setSites($sites)
    {
        $this->sites = json_decode($sites, true);
    }

    public function searchClusters()
    {
        $this->markIgnoredDomains();

        // у старых методов не корректно считываются игнорируемые
        if ($this->engineVersion === 'latest') {
            $this->searchClustersEngineV2();
        } elseif ($this->engineVersion === 'exp') {
            $this->searchClustersEngineV3();
        } elseif ($this->engineVersion === 'exp_phrases') {
            $this->searchClustersEngineV4();
        } elseif ($this->engineVersion === 'maximum') {
            $this->searchClustersEngineV6();
        } else if ($this->engineVersion === 'max_phrases') {
            $this->searchClustersEngine1401();
        } else if ($this->engineVersion === '1601') {
            $this->searchClustersEngine1601();
        }

        if ($this->brutForce && $this->mode === 'professional') {
            $percent = $this->clusteringLevel;
            while ($percent > $this->reductionRatio) {
                $percent = round($percent - 0.1, 1, PHP_ROUND_HALF_ODD);
                if ($this->brutForceType === 'new') {
                    $this->brutForceNew(true);
                } else {
                    $this->brutForceOld($this->count * $percent, true);
                }
            }
        }
    }

    protected function searchClustersEngineV2()
    {
        $willClustered = [];
        foreach ($this->sites as $phrase => $item) {
            foreach ($this->sites as $phrase2 => $item2) {
                if (isset($willClustered[$phrase2])) {
                    continue;
                } else if (isset($this->clusters[$phrase])) {
                    foreach ($this->clusters[$phrase] as $target => $elem) {
                        $int = count(array_intersect(
                            $this->getNotIgnoredDomains($this->sites[$phrase2]['mark']),
                            $this->getNotIgnoredDomains($this->sites[$phrase]['mark'])
                        ));
                        if ($int >= $this->minimum) {
                            $this->clusters[$phrase][$phrase2] = [
                                'based' => $item2['based'],
                                'phrased' => $item2['phrased'],
                                'target' => $item2['target'],
                                'relevance' => $item2['relevance'],
                                'sites' => $item2['sites'],
                                'basedNormal' => $item2['basedNormal'],
                                'merge' => [$int => $phrase]
                            ];
                            $willClustered[$phrase2] = true;
                            break;
                        }
                    }
                } else if (count(array_intersect($this->getNotIgnoredDomains($this->sites[$phrase]['mark']), $this->getNotIgnoredDomains($this->sites[$phrase2]['mark']))) >= $this->minimum) {
                    $this->clusters[$phrase][$phrase2] = [
                        'based' => $item2['based'],
                        'phrased' => $item2['phrased'],
                        'target' => $item2['target'],
                        'relevance' => $item2['relevance'],
                        'sites' => $item2['sites'],
                        'basedNormal' => $item2['basedNormal'],
                        'merge' => [
                            count(array_intersect($this->getNotIgnoredDomains($this->sites[$phrase]['mark']), $this->getNotIgnoredDomains($this->sites[$phrase2]['mark']))) => $phrase
                        ]
                    ];
                    $willClustered[$phrase2] = true;
                }
            }
        }
    }

    protected function searchClustersEngineV3()
    {
        $willClustered = [];
        foreach ($this->sites as $phrase => $item) {
            foreach ($this->sites as $phrase2 => $item2) {
                if (isset($willClustered[$phrase2])) {
                    continue;
                }

                foreach ($this->clusters as $key => $cluster) {
                    foreach ($cluster as $key2 => $clusterItem) {
                        $inter = count(array_intersect(
                            $this->getNotIgnoredDomains($item2['mark']),
                            $this->getNotIgnoredDomains($this->sites[$key2]['mark'])
                        ));
                        if ($inter >= $this->minimum) {
                            $this->clusters[$key][$phrase2] = [
                                'based' => $item2['based'],
                                'phrased' => $item2['phrased'],
                                'target' => $item2['target'],
                                'relevance' => $item2['relevance'],
                                'sites' => $item2['sites'],
                                'basedNormal' => $item2['basedNormal'],
                                'merge' => [$key2 => $inter]
                            ];
                            $willClustered[$phrase2] = true;
                            break 3;
                        }
                    }
                }

                if (count(array_intersect($this->getNotIgnoredDomains($item['mark']), $this->getNotIgnoredDomains($item2['mark']))) >= $this->minimum) {
                    $this->clusters[$phrase][$phrase2] = [
                        'based' => $item2['based'],
                        'phrased' => $item2['phrased'],
                        'target' => $item2['target'],
                        'relevance' => $item2['relevance'],
                        'sites' => $item2['sites'],
                        'basedNormal' => $item2['basedNormal'],
                    ];
                    $willClustered[$phrase2] = true;
                    break;
                }
            }
        }
    }

    protected function searchClustersEngineV4()
    {
        $m = new Morphy();
        $this->wordRatio = [];
        $cache = [];

        foreach ($this->sites as $key1 => $site) {
            $first = explode(' ', $key1);
            if (count($first) === 1) {
                $this->wordRatio[$key1][$key1] = 1;
                continue;
            }

            foreach ($first as $keyF => $item) {
                if (mb_strlen($item) < 2) {
                    continue;
                } elseif (isset($cache[$item])) {
                    $first[$keyF] = $cache[$item];
                } else {
                    $base = $m->base($item);
                    $first[$keyF] = $base;
                    $cache[$item] = $base;
                }
            }

            foreach ($this->sites as $key2 => $site2) {
                $second = explode(' ', $key2);
                foreach ($second as $keyS => $item) {
                    if (mb_strlen($item) < 2) {
                        continue;
                    } elseif (isset($cache[$item])) {
                        $second[$keyS] = $cache[$item];
                    } else {
                        $base = $m->base($item);
                        $second[$keyS] = $base;
                        $cache[$item] = $base;
                    }
                }

                $count = count(array_intersect($first, $second));
                if ($count > 0) {
                    $this->wordRatio[$key1][$key2] = $count;
                }
            }
        }

        $willClustered = [];
        foreach ($this->wordRatio as $mainPhrase => $items) {
            foreach ($items as $phrase => $count) {
                if (isset($willClustered[$phrase])) {
                    continue;
                } else if (isset($this->clusters[$mainPhrase])) {
                    $ph = $this->getNotIgnoredDomains($this->sites[$phrase]['mark']);
                    foreach ($this->clusters[$mainPhrase] as $target => $elem) {
                        $el = $this->getNotIgnoredDomains($elem['sites']);
                        $count = count(array_intersect($ph, $el));
                        if ($count >= $this->minimum) {
                            $this->clusters[$mainPhrase][$phrase] = [
                                'based' => $this->sites[$phrase]['based'],
                                'phrased' => $this->sites[$phrase]['phrased'],
                                'target' => $this->sites[$phrase]['target'],
                                'relevance' => $this->sites[$phrase]['relevance'],
                                'sites' => $this->sites[$phrase]['sites'],
                                'basedNormal' => $this->sites[$phrase]['basedNormal'],
                                'merge' => [$target => $count]
                            ];
                            $willClustered[$phrase] = true;
                            break;
                        }
                    }
                }
                $c = count(array_intersect($this->getNotIgnoredDomains($this->sites[$phrase]['mark']), $this->getNotIgnoredDomains($this->sites[$mainPhrase]['mark'])));
                if ($c >= $this->minimum) {
                    $this->clusters[$mainPhrase][$phrase] = [
                        'based' => $this->sites[$phrase]['based'],
                        'phrased' => $this->sites[$phrase]['phrased'],
                        'target' => $this->sites[$phrase]['target'],
                        'relevance' => $this->sites[$phrase]['relevance'],
                        'sites' => $this->sites[$phrase]['sites'],
                        'basedNormal' => $this->sites[$phrase]['basedNormal'],
                        'merge' => [$mainPhrase => $c]
                    ];
                    $willClustered[$phrase] = true;
                }
            }
        }
    }

    protected function searchClustersEngineV6()
    {
        $pre = [];
        foreach ($this->sites as $phrase => $items) {
            foreach ($this->sites as $ph => $its) {
                $count = count(array_intersect($this->getNotIgnoredDomains($items['mark']), $this->getNotIgnoredDomains($its['mark'])));
                if ($count >= $this->minimum) {
                    $pre[$phrase][$ph] = $count;
                }
            }
            arsort($pre[$phrase]);
        }

        $willClustered = [];
        foreach ($pre as $items) {
            foreach ($items as $phrase => $count) {
                if (isset($willClustered[$phrase])) {
                    continue;
                }
                if ($phrase === array_key_first($items)) {
                    continue;
                }

                $keys = array_keys($pre[$phrase]);
                $keysOf = array_keys($pre[$keys[1]]);

                if ($keysOf[1] === $phrase) {
                    $this->clusters[$phrase][$keys[1]] = $this->sites[$keys[1]];
                    $this->clusters[$phrase][$phrase] = $this->sites[$phrase];
                    $this->clusters[$phrase][$phrase]['merge'] = [$keys[1] => $pre[$phrase][$keys[1]]];
                    $willClustered[$phrase] = true;
                    $willClustered[$keys[1]] = true;
                }
            }
        }

        ksort($this->sites);
        foreach ($this->sites as $mainPhrase => $item) {
            if (isset($willClustered[$mainPhrase])) {
                continue;
            }

            foreach ($this->clusters as $ph => $cluster) {
                foreach ($cluster as $phrase => $val) {
                    $count = count(array_intersect($this->getNotIgnoredDomains($item['mark']), $this->getNotIgnoredDomains($this->sites[$phrase]['mark'])));
                    if ($count >= $this->minimum) {
                        $this->clusters[$ph][$mainPhrase] = $item;
                        $this->clusters[$ph][$mainPhrase]['merge'] = [$phrase => $count];
                        $willClustered[$mainPhrase] = true;
                        continue 3;
                    }
                }
            }
            $this->clusters[$mainPhrase][$mainPhrase] = $item;
            $willClustered[$mainPhrase] = true;
        }

        $willClustered = [];
        foreach ($this->clusters as $mainPhrase => $cluster) {
            if (isset($willClustered[$mainPhrase])) {
                continue;
            }
            foreach ($cluster as $info) {
                $intersects = [];
                foreach ($this->clusters as $offPhrase => $offCluster) {
                    if ($mainPhrase === $offPhrase || isset($willClustered[$offPhrase])) {
                        continue;
                    }
                    foreach ($offCluster as $clusterPhrase => $offInfo) {
                        $count = count(array_intersect($this->getNotIgnoredDomains($info['mark']), $this->getNotIgnoredDomains($offInfo['mark'])));
                        if ($count >= $this->minimum) {
                            $intersects[$offPhrase] = $count;
                        }
                    }
                }
                arsort($intersects);
                if (count($intersects) > 0) {
                    foreach ($intersects as $intersectPhrase => $intersectCount) {
                        foreach ($this->clusters[$intersectPhrase] as $ph => $items) {
                            $intersects2 = [];
                            foreach ($this->clusters as $op => $oc) {
                                if ($op === $intersectPhrase || isset($willClustered[$intersectPhrase])) {
                                    continue;
                                }
                                foreach ($oc as $phrase => $clusterInfo) {
                                    $count2 = count(array_intersect($this->getNotIgnoredDomains($items['mark']), $this->getNotIgnoredDomains($clusterInfo['mark'])));
                                    if ($count2 >= $this->minimum)
                                        $intersects2[$op] = $count2;
                                }
                            }
                        }
                        arsort($intersects2);
                        if (array_key_first($intersects2) === $mainPhrase) {
                            $this->clusters[$mainPhrase] = array_merge($this->clusters[$mainPhrase], $this->clusters[$intersectPhrase]);
                            unset($this->clusters[$intersectPhrase]);
                            $willClustered[$intersectPhrase] = true;
                        }
                    }
                }
            }
        }
    }

    protected function searchClustersEngine1601()
    {
        $m = new Morphy();
        $cache = [];

        uksort($this->sites, function ($a, $b) {
            return mb_strlen($b) - mb_strlen($a);
        });

        foreach ($this->sites as $key1 => $site) {
            $first = explode(' ', $key1);
            if (count($first) === 1) {
                $this->wordRatio[$key1][$key1] = 1;
                continue;
            }

            foreach ($first as $keyF => $item) {
                if (mb_strlen($item) < 2) {
                    continue;
                } elseif (isset($cache[$item])) {
                    $first[$keyF] = $cache[$item];
                } else {
                    $base = $m->base($item);
                    $first[$keyF] = $base;
                    $cache[$item] = $base;
                }
            }

            foreach ($this->sites as $key2 => $site2) {
                $second = explode(' ', $key2);
                foreach ($second as $keyS => $item) {
                    if (mb_strlen($item) < 2) {
                        continue;
                    } elseif (isset($cache[$item])) {
                        $second[$keyS] = $cache[$item];
                    } else {
                        $base = $m->base($item);
                        $second[$keyS] = $base;
                        $cache[$item] = $base;
                    }
                }
                $second = array_diff($second, $this->ignoredWords);

                if (count($second) === 1) {
                    continue;
                }

                $count = count(array_intersect($first, $second));
                if ($count > 0) {
                    $this->wordRatio[$key1][$key2] = $this->minimum - (($this->minimum / 100) * (min($count, 100) * $this->gainFactor));
                }
            }
            if (isset($this->wordRatio[$key1])) {
                arsort($this->wordRatio[$key1]);
            }
        }

        $willClustered = [];
        foreach ($this->wordRatio as $mainPhrase => $phrases) {
            if (isset($willClustered[$mainPhrase])) {
                continue;
            }
            $intersect = [];
            $mainSites = $this->getNotIgnoredDomains($this->sites[$mainPhrase]['mark']);
            foreach ($phrases as $phrase => $minimum) {
                if (isset($willClustered[$phrase]) || $mainPhrase === $phrase) {
                    continue;
                }

                $phraseSites = $this->getNotIgnoredDomains($this->sites[$phrase]['mark']);
                $ideal = count(array_intersect($mainSites, $phraseSites));
                if ($ideal < $minimum) {
                    continue;
                }
                foreach ($this->wordRatio[$phrase] as $ph => $checked) {
                    if ($ph === $phrase || isset($willClustered[$ph])) {
                        continue;
                    }

                    $phSites = $this->getNotIgnoredDomains($this->sites[$ph]['mark']);
                    $c = count(array_intersect($phSites, $phraseSites));
                    if ($c > $checked) {
                        $intersect[$ph] = $c;
                    }
                }

                arsort($intersect);
                if (array_key_first($intersect) === $mainPhrase) {
                    $this->clusters[$mainPhrase][$phrase] = $this->sites[$phrase];
                    $this->clusters[$mainPhrase][$phrase]['merge'] = [$mainPhrase => $intersect[array_key_first($intersect)]];
                    $this->clusters[$mainPhrase][$mainPhrase] = $this->sites[$mainPhrase];
                    $willClustered[$phrase] = true;
                    $willClustered[$mainPhrase] = true;
                }
            }
        }

        foreach ($this->sites as $mainPhrase => $item) {
            if (isset($willClustered[$mainPhrase])) {
                continue;
            }

            $intersect = [];
            foreach ($this->clusters as $ph => $cluster) {
                $max = 0;
                foreach ($cluster as $phrase => $val) {
                    $count = count(array_intersect($this->getNotIgnoredDomains($item['mark']), $this->getNotIgnoredDomains($this->sites[$phrase]['mark'])));
                    if ($count >= $this->minimum && $count > $max) {
                        $max = $count;
                        $intersect[$ph] = [$phrase => $count];
                    }
                }
            }

            if (count($intersect) === 0) {
                $this->clusters[$mainPhrase][$mainPhrase] = $this->sites[$mainPhrase];
            } else {
                uasort($intersect, function ($l, $r) {
                    $first = array_shift($r);
                    $second = array_shift($l);

                    if ($first == $second) return 0;

                    return ($first < $second) ? -1 : 1;
                });

                $mergePhrase = array_key_first($intersect);
                $t = array_shift($intersect);

                $this->clusters[$mergePhrase][$mainPhrase] = $item;
                $this->clusters[$mergePhrase][$mainPhrase]['merge'] = [array_key_first($t) => array_shift($t)];
            }
            $willClustered[$mainPhrase] = true;
        }
    }

    protected function searchClustersEngine1401()
    {
        $m = new Morphy();
        $cache = [];

        uksort($this->sites, function ($a, $b) {
            return mb_strlen($b) - mb_strlen($a);
        });

        foreach ($this->sites as $key1 => $site) {
            $first = explode(' ', $key1);
            if (count($first) === 1) {
                $this->wordRatio[$key1][$key1] = 1;
                continue;
            }

            foreach ($first as $keyF => $item) {
                if (mb_strlen($item) < 2) {
                    continue;
                } elseif (isset($cache[$item])) {
                    $first[$keyF] = $cache[$item];
                } else {
                    $base = $m->base($item);
                    $first[$keyF] = $base;
                    $cache[$item] = $base;
                }
            }

            foreach ($this->sites as $key2 => $site2) {
                $second = explode(' ', $key2);
                foreach ($second as $keyS => $item) {
                    if (mb_strlen($item) < 2) {
                        continue;
                    } elseif (isset($cache[$item])) {
                        $second[$keyS] = $cache[$item];
                    } else {
                        $base = $m->base($item);
                        $second[$keyS] = $base;
                        $cache[$item] = $base;
                    }
                }
                foreach ($second as $key => $phrase) {
                    if (in_array($phrase, $this->ignoredWords)) {
                        unset($second[$key]);
                    }
                }

                if (count($second) === 1) {
                    continue;
                }

                $count = count(array_intersect($first, $second));
                if ($count > 0) {
                    $this->wordRatio[$key1][$key2] = $this->minimum - (($this->minimum / 100) * (min($count, 100) * $this->gainFactor));
                }
            }
            if (isset($this->wordRatio[$key1])) {
                arsort($this->wordRatio[$key1]);
            }
        }

        $willClustered = [];
        foreach ($this->wordRatio as $mainPhrase => $phrases) {
            if (isset($willClustered[$mainPhrase])) {
                continue;
            }
            $intersect = [];
            $mainSites = $this->getNotIgnoredDomains($this->sites[$mainPhrase]['mark']);
            foreach ($phrases as $phrase => $minimum) {
                if (isset($willClustered[$phrase])) {
                    continue;
                }
                if ($mainPhrase === $phrase) {
                    continue;
                }

                $phraseSites = $this->getNotIgnoredDomains($this->sites[$phrase]['mark']);
                $ideal = count(array_intersect($mainSites, $phraseSites));
                if ($ideal < $minimum) {
                    continue;
                }
                $intersect = [];
                foreach ($this->wordRatio[$phrase] as $ph => $checked) {
                    if ($ph === $phrase || isset($willClustered[$ph])) {
                        continue;
                    }

                    $phSites = $this->getNotIgnoredDomains($this->sites[$ph]['mark']);
                    $c = count(array_intersect($phSites, $phraseSites));
                    if ($c > $checked) {
                        $intersect[$ph] = $c;
                    }
                }
                arsort($intersect);
                if (array_key_first($intersect) === $mainPhrase) {
                    $this->clusters[$mainPhrase][$mainPhrase] = $this->sites[$mainPhrase];
                    $this->clusters[$mainPhrase][$phrase] = $this->sites[$phrase];
                    $this->clusters[$mainPhrase][$phrase]['merge'] = [$mainPhrase => $intersect[array_key_first($intersect)]];
                    $willClustered[$phrase] = true;
                    $willClustered[$mainPhrase] = true;
                }
            }
        }

        foreach ($this->sites as $mainPhrase => $item) {
            if (isset($willClustered[$mainPhrase])) {
                continue;
            }

            $intersect = [];
            foreach ($this->clusters as $ph => $cluster) {
                $max = 0;
                foreach ($cluster as $phrase => $val) {
                    $count = count(array_intersect($this->getNotIgnoredDomains($item['mark']), $this->getNotIgnoredDomains($this->sites[$phrase]['mark'])));
                    if ($count >= $this->minimum && $count > $max) {
                        $max = $count;
                        $intersect[$ph] = [$phrase => $count];
                    }
                }
            }

            if (count($intersect) === 0) {
                $this->clusters[$mainPhrase][$mainPhrase] = $this->sites[$mainPhrase];
            } else {
                uasort($intersect, function ($l, $r) {
                    $first = array_shift($r);
                    $second = array_shift($l);

                    if ($first == $second) return 0;

                    return ($first < $second) ? -1 : 1;
                });

                $mergePhrase = array_key_first($intersect);
                $t = array_shift($intersect);

                $this->clusters[$mergePhrase][$mainPhrase] = $item;
                $this->clusters[$mergePhrase][$mainPhrase]['merge'] = [array_key_first($t) => array_shift($t)];
            }
            $willClustered[$mainPhrase] = true;
        }
    }

    public static function getNotIgnoredDomains($sites): array
    {
        $result = [];

        foreach ($sites as $site => $boolean) {
            if ($boolean) {
                continue;
            }
            $result[] = $site;
        }

        return $result;
    }

    protected function brutForceNew($extra = false)
    {
        $willClustered = [];
        foreach ($this->clusters as $firstPhrase => $cluster) {
            if ($extra && count($cluster) > $this->brutForceCount || isset($willClustered[$firstPhrase])) {
                continue;
            }

            foreach ($this->clusters as $secondPhrase => $cluster2) {
                if ($firstPhrase === $secondPhrase || isset($willClustered[$secondPhrase])) {
                    continue;
                }
                $intersects = [];
                foreach ($cluster as $key => $item) {
                    foreach ($cluster2 as $key2 => $item2) {
                        $inter = count(array_intersect($this->getNotIgnoredDomains($item['mark']), $this->getNotIgnoredDomains($item2['mark'])));
                        if (isset($this->wordRatio[$key][$key2]) && $inter >= $this->wordRatio[$key][$key2]) {
                            $intersects[$secondPhrase] = [$key => $key2];
                        }
                    }
                }
                if (count($intersects) > 0) {
                    $ph = array_key_first($intersects);

                    $this->clusters[$firstPhrase] = array_merge($this->clusters[$firstPhrase], $this->clusters[$ph]);
                    $this->clusters[$firstPhrase][$ph]['merge'] = array_shift($intersects);
                    $willClustered[$ph] = true;
                    $willClustered[$firstPhrase] = true;
                    unset($this->clusters[$ph]);
                    continue 2;
                }
            }
        }
    }

    protected function brutForceOld($minimum, $extra = false)
    {
        $willClustered = [];
        foreach ($this->clusters as $firstPhrase => $cluster) {
            if ($extra && count($cluster) > $this->brutForceCount || isset($willClustered[$firstPhrase])) {
                continue;
            }
            foreach ($this->clusters as $secondPhrase => $cluster2) {
                if ($firstPhrase === $secondPhrase || isset($willClustered[$secondPhrase])) {
                    continue;
                }
                foreach ($cluster as $key => $item) {
                    foreach ($cluster2 as $key2 => $item2) {
                        $inter = count(array_intersect($item['sites'], $item2['sites']));
                        if ($inter >= $minimum) {
                            unset($this->clusters[$secondPhrase]);
                            $this->clusters[$firstPhrase] = array_merge($cluster, $cluster2);
                            $this->clusters[$firstPhrase][$secondPhrase]['merge'] = [$key => $inter];
                            $this->clusters[$firstPhrase][$firstPhrase]['merge'] = [$key2 => $inter];
                            $willClustered[$secondPhrase] = true;
                            break 3;
                        }
                    }
                }
            }
        }
    }

    public function calculateClustersInfo()
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

    protected function searchGroupName()
    {
        foreach ($this->clusters as $key => $cluster) {
            $maxRepeatPhrase = 0;
            $groupName = '';
            foreach ($cluster as $phrase => $info) {
                if ($phrase !== 'finallyResult') {
                    if ($maxRepeatPhrase === 0) {
                        $maxRepeatPhrase = $info['based']['number'];
                        $groupName = $info['based']['phrase'];
                    } else if ($info['based']['number'] > $maxRepeatPhrase) {
                        $maxRepeatPhrase = $info['based']['number'];
                        $groupName = $info['based']['phrase'];
                    }
                }
            }
            $this->clusters[$key]['finallyResult']['groupName'] = $groupName;
        }
    }

    protected function setResult(array $results)
    {
        $this->result = collect($results)->sortByDesc(function ($item, $key) {
            return count($item);
        })->values()->all();
    }

    protected function saveResult()
    {
        $this->newCluster = new ClusterResults();
        $result = $this->getResult();
        $this->newCluster->user_id = $this->user->id;
        $this->newCluster->progress_id = $this->getProgressId();
        $this->newCluster->result = base64_encode(gzcompress(json_encode($result), 9));
        $this->newCluster->count_phrases = $this->countPhrases;
        $this->newCluster->count_clusters = count($result);
        $this->newCluster->clustering_level = $this->request['clusteringLevel'];
        $this->newCluster->domain = $this->request['domain'];
        $this->newCluster->comment = $this->request['comment'];
        $this->newCluster->top = $this->count;
        $this->newCluster->request = json_encode($this->request);
        $this->newCluster->sites_json = json_encode($this->sites);
        $this->newCluster->show = $this->save;

        $this->newCluster->save();
    }

    protected function sendNotification()
    {
        if (!$this->user->telegram_bot_active) {
            return;
        }

        $message = "Модуль: <a href='https://lk.redbox.su/cluster'>Кластеризатор</a>
Выполнена задача № " . $this->newCluster->id . "
Домен: " . $this->request['domain'] . "
Комментарий: " . $this->request['comment'] . "
Количество фраз: $this->countPhrases
Количество групп: " . count($this->clusters) . "
Топ: $this->count
Режим: " . $this->request['clusteringLevel'] . "
Регион: " . Common::getRegionName($this->request['region']) . "
<a href='https://lk.redbox.su/show-cluster-result/" . $this->newCluster->id . "'>Просмотр результатов</a>
<a href='https://lk.redbox.su/download-cluster-result/" . $this->newCluster->id . "/csv'>Скачать CSV</a>
<a href='https://lk.redbox.su/download-cluster-result/" . $this->newCluster->id . "/xls'>Скачать XLS</a>";

        TelegramBot::sendMessage($message, $this->user->chat_id);
    }

    protected function mergeClusters($item2, $phrase, $phrase2, array $willClustered = []): array
    {
        $this->clusters[$phrase][$phrase2] = [
            'based' => $item2['based'],
            'phrased' => $item2['phrased'],
            'target' => $item2['target'],
            'relevance' => $item2['relevance'],
            'sites' => $item2['sites'],
            'basedNormal' => $item2['basedNormal'],
        ];
        $willClustered[$phrase2] = true;

        return $willClustered;
    }
}
