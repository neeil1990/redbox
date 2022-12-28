<?php

namespace App;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Jobs\Cluster\ClusterQueue;
use App\Jobs\Cluster\WaitClusterAnalyseQueue;
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

    protected $defaultBrutForce;

    protected $reductionRatio;

    public function __construct(array $request, $user, $default = true)
    {
        $this->brutForce = filter_var($request['brutForce'], FILTER_VALIDATE_BOOLEAN);
        $this->brutForceCount = $request['brutForceCount'] ?? 1;
        $this->reductionRatio = $request['reductionRatio'] ?? 0.4;
        $this->defaultBrutForce = isset($request['defaultBrutForce']) ? filter_var($request['defaultBrutForce'], FILTER_VALIDATE_BOOLEAN) : false;

        if (!isset($request['mode']) || $request['mode'] !== 'professional') {
            $this->count = 40;
            $this->clusteringLevel = 0.6;
        } else {
            if ($request['clusteringLevel'] === 'light') {
                $this->clusteringLevel = 0.4;
            } else if ($request['clusteringLevel'] === 'soft') {
                $this->clusteringLevel = 0.5;
            } else if ($request['clusteringLevel'] === 'pre-hard') {
                $this->clusteringLevel = 0.6;
            } else {
                $this->clusteringLevel = 0.7;
            }
            $this->count = $request['count'];
        }
        $this->minimum = $this->count * $this->clusteringLevel;
        $this->mode = $request['mode'];
        $this->engineVersion = $request['engineVersion'];

        if ($default) {
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
            $this->request = $request;
            $this->progressId = $request['progressId'];

            $this->xml = new SimplifiedXmlFacade($this->region, $this->count);

            $this->host = $this->searchRelevance ? parse_url($this->request['domain'])['host'] : $this->request['domain'];
        }
    }

    public function __sleep()
    {
        return [
            'count', 'region', 'phrases', 'clusteringLevel', 'countPhrases',
            'sites', 'result', 'clusters', 'engineVersion', 'searchBase', 'searchPhrases',
            'searchTarget', 'searchRelevance', 'searchEngine', 'progress', 'save', 'request',
            'newCluster', 'user', 'brutForce', 'xml', 'host', 'mode', 'minimum', 'progressId',
            'brutForceCount', 'reductionRatio', 'defaultBrutForce'
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
        if ($this->engineVersion === 'old') {
            $this->searchClustersEngineV1();
        } else if ($this->engineVersion === 'latest') {
            $this->searchClustersEngineV2();
        } elseif ($this->engineVersion === 'exp') {
            $this->searchClustersEngineV3();
        } elseif ($this->engineVersion === 'exp_phrases') {
            $this->searchClustersEngineV4();
        } elseif ($this->engineVersion === 'maximum') {
            $this->searchClustersEngineV6();
        }

        if ($this->defaultBrutForce) {
            $this->brutForceClusters($this->minimum);
        }

        if ($this->brutForce && $this->mode === 'professional') {
            $percent = $this->clusteringLevel;
            while ($percent > $this->reductionRatio) {
                $percent = round($percent - 0.1, 1, PHP_ROUND_HALF_ODD);
                $this->brutForceClusters($this->count * $percent, true);
            }
        }
    }

    protected function searchClustersEngineV1()
    {
        $willClustered = [];

        foreach ($this->sites as $phrase => $sites) {
            foreach ($this->sites as $phrase2 => $sites2) {
                if (isset($willClustered[$phrase2])) {
                    continue;
                } elseif (count(array_intersect($sites['sites'], $sites2['sites'])) >= $this->minimum) {
                    $willClustered = $this->mergeClusters($sites2, $phrase, $phrase2, $willClustered);
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
                        if (count(array_intersect($item2['sites'], $elem['sites'])) >= $this->minimum) {
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
                } else if (count(array_intersect($item['sites'], $item2['sites'])) >= $this->minimum) {
                    $this->clusters[$phrase][$phrase2] = [
                        'based' => $item2['based'],
                        'phrased' => $item2['phrased'],
                        'target' => $item2['target'],
                        'relevance' => $item2['relevance'],
                        'sites' => $item2['sites'],
                        'basedNormal' => $item2['basedNormal'],
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
                        if (count(array_intersect($item2['sites'], $clusterItem['sites'])) >= $this->minimum) {
                            $this->clusters[$key][$phrase2] = [
                                'based' => $item2['based'],
                                'phrased' => $item2['phrased'],
                                'target' => $item2['target'],
                                'relevance' => $item2['relevance'],
                                'sites' => $item2['sites'],
                                'basedNormal' => $item2['basedNormal'],
                                'merge' => [$key2 => count(array_intersect($item2['sites'], $clusterItem['sites']))]
                            ];
                            $willClustered[$phrase2] = true;
                            break 3;
                        }
                    }
                }

                if (count(array_intersect($item['sites'], $item2['sites'])) >= $this->minimum) {
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
        $result = [];
        $cache = [];

        foreach ($this->sites as $key1 => $site) {
            $first = explode(' ', $key1);
            if (count($first) === 1) {
                $result[$key1][$key1] = 1;
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
                    $result[$key1][$key2] = $count;
                }
            }
        }

        $willClustered = [];
        foreach ($result as $mainPhrase => $items) {
            foreach ($items as $phrase => $count) {
                if (isset($willClustered[$phrase])) {
                    continue;
                } else if (isset($this->clusters[$mainPhrase])) {
                    foreach ($this->clusters[$mainPhrase] as $target => $elem) {
                        $count = count(array_intersect($this->sites[$phrase]['sites'], $elem['sites']));
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
                } else if (count(array_intersect($this->sites[$phrase]['sites'], $this->sites[$mainPhrase]['sites'])) >= $this->minimum) {
                    $this->clusters[$mainPhrase][$phrase] = [
                        'based' => $this->sites[$phrase]['based'],
                        'phrased' => $this->sites[$phrase]['phrased'],
                        'target' => $this->sites[$phrase]['target'],
                        'relevance' => $this->sites[$phrase]['relevance'],
                        'sites' => $this->sites[$phrase]['sites'],
                        'basedNormal' => $this->sites[$phrase]['basedNormal'],
                        'merge' => [$mainPhrase => count(array_intersect($this->sites[$phrase]['sites'], $this->sites[$mainPhrase]['sites']))]
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
                $count = count(array_intersect($items['sites'], $its['sites']));
                if ($count >= $this->minimum) {
                    $pre[$phrase][$ph] = count(array_intersect($items['sites'], $its['sites']));
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
                    $count = count(array_intersect($item['sites'], $this->sites[$phrase]['sites']));
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
                        $count = count(array_intersect($info['sites'], $offInfo['sites']));
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
                                    $count2 = count(array_intersect($items['sites'], $clusterInfo['sites']));
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

    protected function brutForceClusters($minimum, $extra = false)
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
                        if (
                            isset($item['sites']) && isset($item2['sites']) &&
                            count(array_intersect($item['sites'], $item2['sites'])) >= $minimum
                        ) {
                            unset($this->clusters[$secondPhrase]);
                            $cluster2[$key2]['merge'] = [$key => count(array_intersect($item['sites'], $item2['sites']))];
                            $this->clusters[$firstPhrase] = array_merge($cluster, $cluster2);
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
Регион: " . Cluster::getRegionName($this->request['region']) . "
<a href='https://lk.redbox.su/show-cluster-result/" . $this->newCluster->id . "'>Просмотр результатов</a>
<a href='https://lk.redbox.su/download-cluster-result/" . $this->newCluster->id . "/csv'>Скачать CSV</a>
<a href='https://lk.redbox.su/download-cluster-result/" . $this->newCluster->id . "/xls'>Скачать XLS</a>";

        TelegramBot::sendMessage($message, $this->user->chat_id);
    }

    public static function getRegionName(string $id): string
    {
        switch ($id) {
            case '1' :
                return __('Moscow');
            case '20' :
                return __('Arkhangelsk');
            case '37' :
                return __('Astrakhan');
            case '197' :
                return __('Barnaul');
            case '4' :
                return __('Belgorod');
            case '77' :
                return __('Blagoveshchensk');
            case '191' :
                return __('Bryansk');
            case '24' :
                return __('Veliky Novgorod');
            case '75' :
                return __('Vladivostok');
            case '33' :
                return __('Vladikavkaz');
            case '192' :
                return __('Vladimir');
            case '38' :
                return __('Volgograd');
            case '21' :
                return __('Vologda');
            case '193' :
                return __('Voronezh');
            case '1106' :
                return __('Grozny');
            case '54' :
                return __('Ekaterinburg');
            case '5' :
                return __('Ivanovo');
            case '63' :
                return __('Irkutsk');
            case '41' :
                return __('Yoshkar-ola');
            case '43' :
                return __('Kazan');
            case '22' :
                return __('Kaliningrad');
            case '64' :
                return __('Kemerovo');
            case '7' :
                return __('Kostroma');
            case '35' :
                return __('Krasnodar');
            case '62' :
                return __('Krasnoyarsk');
            case '53' :
                return __('Kurgan');
            case '8' :
                return __('Kursk');
            case '9' :
                return __('Lipetsk');
            case '28' :
                return __('Makhachkala');
            case '213' :
                return __('Moscow');
            case '23' :
                return __('Murmansk');
            case '1092' :
                return __('Nazran');
            case '30' :
                return __('Nalchik');
            case '47' :
                return __('Nizhniy Novgorod');
            case '65' :
                return __('Novosibirsk');
            case '66' :
                return __('Omsk');
            case '10' :
                return __('Eagle');
            case '48' :
                return __('Orenburg');
            case '49' :
                return __('Penza');
            case '50' :
                return __('Perm');
            case '25' :
                return __('Pskov');
            case '39' :
                return __('Rostov-on');
            case '11' :
                return __('Ryazan');
            case '51' :
                return __('Samara');
            case '42' :
                return __('Saransk');
            case '2' :
                return __('Saint-Petersburg');
            case '12' :
                return __('Smolensk');
            case '239' :
                return __('Sochi');
            case '36' :
                return __('Stavropol');
            case '973' :
                return __('Surgut');
            case '13' :
                return __('Tambov');
            case '14' :
                return __('Tver');
            case '67' :
                return __('Tomsk');
            case '15' :
                return __('Tula');
            case '195' :
                return __('Ulyanovsk');
            case '172' :
                return __('Ufa');
            case '76' :
                return __('Khabarovsk');
            case '45' :
                return __('Cheboksary');
            case '56' :
                return __('Chelyabinsk');
            case '1104' :
                return __('Cherkessk');
            case '16' :
                return __('Yaroslavl');
            default:
                return 'Регион не опознан';
        }
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
