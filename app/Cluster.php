<?php

namespace App;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Jobs\Cluster\ClusterQueue;
use App\Jobs\Cluster\WaitClusterAnalyseQueue;

class Cluster
{
    public $count;

    public $region;

    public $phrases;

    public $clusteringLevel;

    public $countPhrases;

    public $sites;

    public $result;

    public $clusters = [];

    public $engineVersion;

    public $searchBase;

    public $searchPhrases;

    public $searchTarget;

    public $searchRelevance;

    public $searchEngine;

    public $progress;

    public $save;

    public $request;

    public $newCluster;

    protected $user;

    public $brutForce;

    public $xml;

    public $host;

    public $mode;

    public $minimum;

    public $progressId;

    public $brutForceCount;

    public $reductionRatio;

    public $ignoredWords;

    public $ignoredDomains;

    public $gainFactor;

    public $wordRatio = [];

    public function __construct(array $request, $user, $default = true)
    {
        if ($request['mode'] === 'classic') {
            $config = ClusterConfigurationClassic::first();
            $this->searchEngine = $config->search_engine;
            $this->gainFactor = (int)$config->gain_factor;
            $this->count = (int)$config->count;
            $this->setReductionRatio($config->reduction_ratio);
            $this->brutForceCount = (int)$config->brut_force_count;
            $this->brutForce = $config->brut_force;
            $this->ignoredWords = explode("\r\n", $config->ignored_words);
            $this->ignoredDomains = explode("\r\n", $config->ignored_domains);
            $this->engineVersion = $config->engine_version;
        } else {
            $config = ClusterConfiguration::first();
            $this->ignoredWords = isset($request['ignoredWords']) ? explode("\n", $request['ignoredWords']) : [];
            $this->ignoredDomains = isset($request['ignoredDomains']) ? explode("\n", $request['ignoredDomains']) : [];
            $this->searchEngine = $request['searchEngine'] ?? $config->search_engine;
            $this->gainFactor = (int)$request['gainFactor'];
            $this->count = (int)$request['count'];
            $this->setReductionRatio($request['reductionRatio']);
            $this->brutForceCount = (int)$request['brutForceCount'];
            $this->brutForce = filter_var($request['brutForce'], FILTER_VALIDATE_BOOLEAN);
            $this->engineVersion = $request['engineVersion'] ?? $config->engine_version;
        }

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

        $this->minimum = $this->count * $this->clusteringLevel;
        $this->mode = $request['mode'];

        if ($default) {
            $this->user = $user;
            $this->request = $request;
            $this->request['engineVersion'] = $this->engineVersion;
            $this->request['count'] = $this->count;
            $this->searchBase = filter_var($request['searchBase'], FILTER_VALIDATE_BOOLEAN);
            $this->searchRelevance = filter_var($request['searchRelevance'], FILTER_VALIDATE_BOOLEAN);
            $this->searchPhrases = filter_var($request['searchPhrases'], FILTER_VALIDATE_BOOLEAN);
            $this->searchTarget = filter_var($request['searchTarget'], FILTER_VALIDATE_BOOLEAN);
            $this->save = filter_var($request['save'], FILTER_VALIDATE_BOOLEAN);
            $this->phrases = array_unique(array_diff(explode("\n", str_replace("\r", "", $request['phrases'])), []));
            $this->countPhrases = count($this->phrases);
            $this->progressId = $request['progressId'];
            $this->region = $request['region'];

            $this->xml = new SimplifiedXmlFacade($this->region, 100);

            $this->host = $this->searchRelevance ? parse_url($this->request['domain'])['host'] : $this->request['domain'];
        }

    }

    protected function setReductionRatio(string $ratio)
    {
        if ($ratio === 'pre-hard') {
            $this->reductionRatio = 0.6;
        } else if ($ratio === 'soft') {
            $this->reductionRatio = 0.5;
        }
    }

    public function __sleep()
    {
        return [
            'count', 'region', 'phrases', 'clusteringLevel', 'countPhrases', 'sites', 'result',
            'clusters', 'engineVersion', 'searchBase', 'searchPhrases', 'searchTarget',
            'searchRelevance', 'searchEngine', 'progress', 'save', 'request', 'newCluster',
            'user', 'brutForce', 'xml', 'host', 'mode', 'minimum', 'progressId', 'brutForceCount',
            'reductionRatio', 'ignoredWords', 'ignoredDomains', 'gainFactor', 'wordRatio',
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
        $this->clusters = $this->calculateSimilarities($this->clusters, $this->ignoredWords);
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
            unset($this->sites[$phrase]['mark']);
        }

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

    public function setSites($sites)
    {
        $this->sites = json_decode($sites, true);
    }

    public function searchClusters()
    {
        $this->markIgnoredDomains();

        if ($this->engineVersion === 'max_phrases') {
            $this->searchClustersEngine1301();
        } else if ($this->engineVersion === '1501') {
            $this->searchClustersEngine1501();
        }

        if ($this->brutForce) {
            $percent = $this->clusteringLevel;
            while ($percent > $this->reductionRatio) {
                $percent = round($percent - 0.1, 1, PHP_ROUND_HALF_ODD);
                $this->brutForce($this->count * $percent);
            }
        }
    }

    protected function searchClustersEngine1501()
    {
        $m = new Morphy();
        $cache = [];

        uksort($this->sites, function ($a, $b) {
            return mb_strlen($b) - mb_strlen($a) ?: strcmp($a, $b);
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
                ksort($this->wordRatio[$key1]);
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

                ksort($intersect);
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

    protected function searchClustersEngine1301()
    {
        $m = new Morphy();
        $cache = [];

        uksort($this->sites, function ($a, $b) {
            return mb_strlen($b) - mb_strlen($a) ?: strcmp($a, $b);
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
                ksort($this->wordRatio[$key1]);
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

    protected function brutForce($minimum)
    {
        $willClustered = [];
        foreach ($this->clusters as $firstPhrase => $cluster) {
            if (count($cluster) > $this->brutForceCount || isset($willClustered[$firstPhrase])) {
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
                        if (
                            isset($this->wordRatio[$key][$key2]) &&
                            $inter >= $this->wordRatio[$key][$key2] ||
                            $inter > $minimum
                        ) {
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

    public static function recalculateClustersInfo(array $clusters, $searchBase = false): array
    {
        foreach ($clusters as $key => $phrases) {
            if (count($phrases) === 1 && array_key_first($phrases) === 'finallyResult') {
                unset($clusters[$key]);
                continue;
            }

            $merge = [];
            foreach ($phrases as $phrase => $sites) {
                $merge = array_merge($merge, $sites['sites']);
            }
            $merge = array_count_values($merge);
            arsort($merge);
            $clusters[$key]['finallyResult']['sites'] = $merge;
        }

        ksort($clusters);
        arsort($clusters);

        if (filter_var($searchBase, FILTER_VALIDATE_BOOLEAN)) {
            foreach ($clusters as $key => $cluster) {
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
                $clusters[$key]['finallyResult']['groupName'] = $groupName;
            }
        }

        return [
            'clusters' => base64_encode(gzcompress(json_encode($clusters), 9)),
            'countClusters' => count($clusters)
        ];
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

    public static function calculateSimilarities($clusters, $ignoredWords)
    {
        $m = new Morphy();

        foreach ($clusters as $mainPhrase => $items) {
            foreach ($items as $offPhrase => $info) {
                $phrase = explode(' ', $offPhrase);
                $phrase = array_diff($phrase, $ignoredWords);

                foreach ($phrase as $keyF => $item) {
                    if (mb_strlen($item) < 2) {
                        continue;
                    } else {
                        $base = $m->base($item);
                        $phrase[$keyF] = $base;
                    }
                }

                foreach ($clusters as $mainPhrase2 => $items2) {
                    if ($mainPhrase === $mainPhrase2) {
                        continue;
                    }
                    foreach ($items2 as $offPhrase2 => $info2) {
                        if ($offPhrase === $offPhrase2 || $offPhrase === 'finallyResult' || $offPhrase2 === 'finallyResult') {
                            continue;
                        }
                        $phrase2 = explode(' ', $offPhrase2);
                        $phrase2 = array_diff($phrase2, $ignoredWords);
                        foreach ($phrase2 as $keyF => $item) {
                            if (mb_strlen($item) < 2) {
                                continue;
                            } else {
                                $base = $m->base($item);
                                $phrase2[$keyF] = $base;
                            }
                            $similarities = count(array_intersect($phrase, $phrase2));
                            if ($similarities > 1) {
                                $clusters[$mainPhrase][$offPhrase]['similarities'][$offPhrase2] = $similarities;
                            }
                        }
                    }
                }

                if (isset($clusters[$mainPhrase][$offPhrase]['similarities'])) {
                    arsort($clusters[$mainPhrase][$offPhrase]['similarities']);
                }
            }
        }

        return $clusters;
    }

    protected function setResult(array $results)
    {
        $this->result = $results;
        ksort($this->result);
        arsort($this->result);
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

    public static function recalculateClusterInfo(ClusterResults $cluster, array $clusters)
    {
        $request = json_decode($cluster->request, true);
        $ignoredWords = isset($request['ignoredWords']) ? explode("\n", $request['ignoredWords']) : [];
        $clusters = Cluster::calculateSimilarities($clusters, $ignoredWords);
        $result = Cluster::recalculateClustersInfo($clusters, $request['searchBase']);
        $cluster->result = $result['clusters'];
        $cluster->count_clusters = $result['countClusters'];
        $cluster->save();
    }
}
