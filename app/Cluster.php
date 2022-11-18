<?php

namespace App;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Jobs\ClusterQueue;
use App\Jobs\WaitClusterAnalyse;
use Illuminate\Support\Facades\Log;
use Throwable;

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

    protected $save;

    protected $request;

    protected $newCluster;

    protected $sites_json;

    protected $percent;

    protected $user;

    public function __construct(array $request, $user, $default = true)
    {
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


        if ($default) {
            $this->engineVersion = $request['engineVersion'];
            $this->user = $user;
            $this->region = $request['region'];
            $this->searchPhrases = filter_var($request['searchPhrases'], FILTER_VALIDATE_BOOLEAN);
            $this->searchTarget = filter_var($request['searchTarget'], FILTER_VALIDATE_BOOLEAN);
            $this->save = filter_var($request['save'], FILTER_VALIDATE_BOOLEAN);
            $this->phrases = array_unique(array_diff(explode("\n", str_replace("\r", "", $request['phrases'])), []));
            $this->countPhrases = count($this->phrases);
            $this->request = $request;

            $this->progress = ClusterProgress::where('id', '=', $request['progressId'])->first();
        }
    }

    public function __sleep()
    {
        return [
            'count', 'region', 'phrases', 'clusteringLevel',
            'countPhrases', 'sites', 'result', 'clusters',
            'engineVersion', 'searchPhrases', 'searchTarget',
            'progress', 'save', 'request', 'newCluster',
            'sites_json', 'percent', 'user',
        ];
    }

    public function startAnalysis()
    {
        try {
            $this->parseSites();
            $this->searchClusters();
            $this->calculateClustersInfo();
            $this->wordStats();
        } catch (Throwable $e) {
            Log::debug('cluster error', [
                $e->getMessage(),
                $e->getLine(),
                $e->getFile()
            ]);
            $this->progress->delete();
            \App\ClusterQueue::where('progress_id', '=', $this->progress->id)->delete();
        }
    }

    protected function parseSites()
    {
        $percent = 49 / $this->countPhrases;
        $iterator = 0;
        $xml = new SimplifiedXmlFacade($this->region, $this->count);
        foreach ($this->phrases as $key => $phrase) {
            $this->progress->percent += $percent;
            $iterator++;
            $xml->setQuery($phrase);
            $sites = $xml->getXMLResponse();
            if ($sites !== null) {
                $this->sites[$phrase]['sites'] = $sites;
            } else {
                unset($this->phrases[$key]);
            }
            if ($iterator % 3 === 0 || $phrase === end($this->phrases)) {
                $this->progress->save();
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
        $this->sites_json = json_encode($this->sites);
        $minimum = $this->count * $this->clusteringLevel;

        if ($this->engineVersion === 'old') {
            $this->searchClustersEngineV1($minimum);
        } else if ($this->engineVersion === 'new') {
            $this->searchClustersEngineV2($minimum);
        } else {
            $this->searchClustersEngineV2($minimum);
            $this->brutForceClusters($minimum + 2);
        }
    }

    /**
     * @return array|mixed
     */
    public function getClusters()
    {
        return $this->clusters;
    }

    protected function searchClustersEngineV1($minimum)
    {
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

    protected function searchClustersEngineV2($minimum)
    {
        $willClustered = [];

        foreach ($this->sites as $phrase => $item) {
            foreach ($this->sites as $phrase2 => $item2) {
                if (isset($willClustered[$phrase2])) {
                    continue;
                } else if (isset($this->clusters[$phrase])) {
                    foreach ($this->clusters[$phrase] as $target => $elem) {
                        if (count(array_intersect($item2['sites'], $elem['sites'])) >= $minimum) {
                            $this->clusters[$phrase][$phrase2] = ['sites' => $item2['sites']];
                            $willClustered[$phrase2] = true;
                            break;
                        }
                    }
                } else if (count(array_intersect($item['sites'], $item2['sites'])) >= $minimum) {
                    $this->clusters[$phrase][$phrase2] = ['sites' => $item2['sites']];
                    $willClustered[$phrase2] = true;
                }
            }
        }
    }

    protected function brutForceClusters($minimum)
    {
        foreach ($this->clusters as $keyPhrase => $cluster) {
            foreach ($this->clusters as $anotherKeyPhrase => $anotherCluster) {
                if ($keyPhrase === $anotherKeyPhrase) {
                    break;
                }
                foreach ($cluster as $key1 => $elems) {
                    foreach ($anotherCluster as $key2 => $anotherElems) {
                        if (isset($elems['sites']) && isset($anotherElems['sites'])) {
                            if (count(array_intersect($elems['sites'], $anotherElems['sites'])) >= $minimum) {
                                $this->clusters[$keyPhrase] = array_merge_recursive($cluster, $anotherCluster);
                                $this->clusters[$keyPhrase][$anotherKeyPhrase]['merge'] = [$key1 => $key2];
                                unset($this->clusters[$anotherKeyPhrase]);
                                break 2;
                            }
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

    protected function wordStats()
    {
        $this->progress->total = $this->calculateCountRequests();
        $this->progress->save();
        $this->percent = 50 / $this->progress->total;

        foreach ($this->clusters as $key => $cluster) {
            foreach ($cluster as $phrase => $sites) {
                if ($phrase !== 'finallyResult') {
                    if ($this->searchPhrases) {
                        $this->tryInitJob(
                            '"' . $phrase . '"',
                            $key,
                            $phrase,
                            'phrased'
                        );
                    }

                    if ($this->searchTarget) {
                        $this->tryInitJob(
                            '"!' . implode(' !', explode(' ', $phrase)) . '"',
                            $key,
                            $phrase,
                            'target'
                        );
                    }

                    $this->tryInitJob($phrase, $key, $phrase, 'based');
                }
            }
        }

        dispatch(new WaitClusterAnalyse($this))->onQueue('wait_cluster');
    }

    public function getProgressTotal(): int
    {
        return $this->progress->total;
    }

    public function getProgressCurrentCount(): int
    {
        return \App\ClusterQueue::where('progress_id', '=', $this->progress->id)->count();
    }

    protected function tryInitJob($phrase, $key, $keyPhrase, $type)
    {
        ClusterQueue::dispatch(
            $this->region,
            $this->progress->id,
            $this->percent,
            $phrase,
            $key,
            $keyPhrase,
            $type
        )->onQueue('child_cluster');
    }

    public function setRiverResults()
    {
        $array = [];
        $results = \App\ClusterQueue::where('progress_id', '=', $this->progress->id)->get();
        foreach ($results as $result) {
            $array = array_merge_recursive($array, json_decode($result->json, true));
        }

        foreach ($this->clusters as $key => $cluster) {
            foreach ($cluster as $phrase => $sites) {
                if ($phrase !== 'finallyResult') {
                    if ($this->searchPhrases) {
                        $this->clusters[$key][$phrase]['phrased'] = $array[$key][$phrase]['phrased'];
                    }

                    if ($this->searchTarget) {
                        $this->clusters[$key][$phrase]['target'] = $array[$key][$phrase]['target'];
                    }

                    $this->clusters[$key][$phrase]['based'] = $array[$key][$phrase]['based'];
                    if ($array[$key][$phrase]['based']['phrase'] !== $phrase) {
                        $this->clusters[$key][$phrase]['basedNormal'] = $array[$key][$phrase]['based']['phrase'];
                    }
                }
            }
        }

        $this->searchGroupName();
        $this->setResult($this->clusters);
        $this->saveResult();

        if (isset($this->request['sendMessage']) && filter_var($this->request['sendMessage'], FILTER_VALIDATE_BOOLEAN)) {
            $this->sendNotification();
        }

        $this->progress->delete();
        \App\ClusterQueue::where('progress_id', '=', $this->progress->id)->delete();
        $this->progress->total = 0;
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
     * @return void
     */
    protected function saveResult()
    {
        $this->newCluster = new ClusterResults();
        $result = $this->getResult();
        $this->newCluster->user_id = $this->user->id;
        $this->newCluster->progress_id = $this->progress->id;
        $this->newCluster->result = base64_encode(gzcompress(json_encode($result), 9));
        $this->newCluster->count_phrases = $this->countPhrases;
        $this->newCluster->count_clusters = count($result);
        $this->newCluster->clustering_level = $this->request['clusteringLevel'];
        $this->newCluster->domain = $this->request['domain'];
        $this->newCluster->comment = $this->request['comment'];
        $this->newCluster->top = $this->count;
        $this->newCluster->request = json_encode($this->request);
        $this->newCluster->sites_json = $this->sites_json;
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

    /**
     * @return mixed
     */
    public function getNewCluster()
    {
        return $this->newCluster;
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

    public static function fastScanClusters()
    {

    }
}
