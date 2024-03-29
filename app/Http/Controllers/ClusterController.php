<?php

namespace App\Http\Controllers;

use App\Cluster;
use App\ClusterConfiguration;
use App\ClusterConfigurationClassic;
use App\ClusterLimit;
use App\ClusterQueue;
use App\ClusterResults;
use App\Common;
use App\Exports\Cluster\ClusterGroupExport;
use App\Exports\Cluster\ClusterResultExport;
use App\Jobs\Cluster\StartClusterAnalyseQueue;
use App\User;
use Carbon\Carbon;
use Doctrine\DBAL\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ClusterController extends Controller
{
    public function index($result = null): View
    {
        $admin = User::isUserAdmin();

        return view('cluster.index', [
            'admin' => $admin,
            'results' => $result,
            'config' => ClusterConfiguration::first(),
            'config_classic' => ClusterConfigurationClassic::first()
        ]);
    }

    public function analyseCluster(Request $request): JsonResponse
    {
        $this->validate($request, [
            'domain' => 'sometimes|required_if:searchRelevance,==,true',
        ], [
            'domain.required_if' => __('the domain is required if the relevant page selection mode is selected')
        ]);

        $countRequests = ClusterLimit::calculateCountRequests($request->all());
        if (ClusterLimit::checkClustersLimits($countRequests)) {
            return response()->json([
                'errors' => ['limits' => __('Your limits are exhausted')]
            ], 422);
        }

        if (filter_var($request->input('searchRelevance'), FILTER_VALIDATE_BOOL)) {
            $link = parse_url($request->input('domain'));
            if (empty($link['host'])) {
                return response()->json([
                    'errors' => ['domain' => __('url not valid')]
                ], 422);
            }
        }
        $user = Auth::user();
        dispatch(new StartClusterAnalyseQueue($request->all(), $user))->onQueue('main_cluster');

        return response()->json([
            'result' => true,
            'totalPhrases' => count(array_unique(array_diff(explode("\n", str_replace("\r", "", $request['phrases'])), []))),
            'totalRequests' => $countRequests,
        ]);
    }

    public function startProgress(): JsonResponse
    {
        return response()->json([
            'id' => md5(microtime(true))
        ], 201);
    }

    public function getProgress(string $id): JsonResponse
    {
        $cluster = ClusterResults::where('progress_id', '=', $id)->first();
        if (isset($cluster)) {
            ClusterQueue::where('progress_id', '=', $id)->delete();
            return response()->json([
                'count' => $cluster->count_phrases,
                'result' => Cluster::unpackCluster($cluster->result),
                'objectId' => $cluster->id,
            ]);
        }

        return response()->json([
            'count' => ClusterQueue::where('progress_id', '=', $id)->count(),
        ]);
    }

    public function getProgressModify($id): JsonResponse
    {
        $cluster = ClusterResults::where('progress_id', '=', $id)->first();
        if (isset($cluster)) {
            ClusterQueue::where('progress_id', '=', $id)->delete();
            $cluster->request = json_decode($cluster->request, true);
            $cluster->region = Common::getRegionName($cluster->request['region']);
            return response()->json([
                'cluster' => $cluster,
            ]);
        }

        return response()->json([
            'count' => ClusterQueue::where('progress_id', '=', $id)->count(),
        ]);
    }

    public function fastScanClusters(Request $request): JsonResponse
    {
        $user = Auth::user();
        $cluster = new Cluster($request->all(), $user, false);
        $results = ClusterResults::findOrFail($request->input('resultId'));
        $cluster->setSites($results->sites_json);
        $cluster->searchClusters();
        $cluster->calculateClustersInfo();
        $clusters = $cluster->getClusters();
        ksort($clusters);
        arsort($clusters);

        return response()->json([
            'sites' => $clusters,
            'count' => count($clusters)
        ]);
    }

    public function clusterProjects(): View
    {
        $admin = User::isUserAdmin();
        $projects = ClusterResults::where('user_id', '=', Auth::id())
            ->where('show', '=', 1)->get([
                'id', 'user_id', 'comment', 'domain', 'count_phrases', 'count_clusters', 'clustering_level',
                'top', 'created_at', 'request'
            ]);

        foreach ($projects as $key => $project) {
            $request = json_decode($project->request, true);
            $project->region = Common::getRegionName($request['region']);
            $project->request = $request;
        }

        return view('cluster.projects', ['projects' => $projects, 'admin' => $admin, 'config' => ClusterConfiguration::first()]);
    }

    public function edit(Request $request): JsonResponse
    {
        ClusterResults::where('id', $request->id)
            ->where('user_id', '=', Auth::id())
            ->update([$request->option => $request->value]);

        return response()->json([]);
    }

    public function getClusterRequest(Request $request): JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->id)->first();

        if ($cluster->user_id !== Auth::id()) {
            return response()->json([
                'message' => __("You don't have access to this object")
            ], 500);
        }

        return response()->json([
            'created_at' => Carbon::parse($cluster->created_at)->toDateTimeString(),
            'request' => json_decode($cluster->request, true)
        ]);
    }

    public function showResult(int $id): View
    {
        $cluster = ClusterResults::where('id', $id)->first([
            'count_clusters', 'count_phrases',
            'default_result', 'result',
            'request', 'user_id',
            'id', 'show'
        ]);

        if (($cluster->user_id == Auth::id() || User::isUserAdmin()) && $cluster->show === 1) {

            $cluster->result = isset($cluster->default_result)
                ? Common::uncompressArray($cluster->default_result, false)
                : Common::uncompressArray($cluster->result, false);
            unset($cluster->default_result);

            $cluster->request = json_decode($cluster->request, true);

            return view('cluster.show', ['cluster' => $cluster->toArray(), 'admin' => User::isUserAdmin()]);
        }

        return abort(403);
    }

    public function setClusterRelevanceUrl(Request $request): JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->input('projectId'))
            ->where('user_id', '=', Auth::id())
            ->first();

        if (isset($cluster)) {
            if ($request->input('type') === 'notDefault') {
                $results = Cluster::unpackCluster($cluster->result);
            } else {
                $results = Cluster::unpackCluster($cluster->default_result);
            }

            foreach ($results as $key => $items) {
                foreach ($items as $phrase => $item) {
                    if ($phrase === $request->input('phrase')) {
                        $results[$key][$phrase]['link'] = $request->input('url');
                        unset($results[$key][$phrase]['relevance']);
                    }
                }
            }

            if ($request->input('type') === 'notDefault') {
                $cluster->result = base64_encode(gzcompress(json_encode($results), 9));
            } else {
                $cluster->default_result = base64_encode(gzcompress(json_encode($results), 9));
            }

            $cluster->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }

    public function setClusterRelevanceUrls(Request $request): JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->input('projectId'))
            ->where('user_id', '=', Auth::id())
            ->first();

        if (isset($cluster)) {
            if ($request->input('type') === 'notDefault') {
                $results = Cluster::unpackCluster($cluster->result);
            } else {
                $results = Cluster::unpackCluster($cluster->default_result);
            }

            foreach ($results as $key => $items) {
                foreach ($items as $phrase => $item) {
                    if (in_array($phrase, $request->input('phrases'))) {
                        $results[$key][$phrase]['link'] = $request->input('url');
                        unset($results[$key][$phrase]['relevance']);
                    }
                }
            }

            if ($request->input('type') === 'notDefault') {
                $cluster->result = base64_encode(gzcompress(json_encode($results), 9));
            } else {
                $cluster->default_result = base64_encode(gzcompress(json_encode($results), 9));
            }
            $cluster->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }

    public function downloadClusterResult(ClusterResults $cluster, string $type)
    {
        if ($cluster->created_at <= Carbon::parse('00:00 22.02.2023')) {
            return abort(403, __('In order to edit this result, you need to reshoot it'));
        }

        if ((User::isUserAdmin() || $cluster->user_id == Auth::id()) && ($type === 'xls' || $type === 'csv')) {
            if (isset($cluster->domain)) {
                $domain = str_replace(['https://', 'http://'], '', $cluster->domain);
                $fileName = Carbon::parse($cluster->created_at)->toDateString() . '-' . str_replace(['.', '/', ' '], '-', $domain);
            } else {
                $fileName = Carbon::parse($cluster->created_at)->toDateString() . '-' . $cluster->id;
            }

            $file = Excel::download(new ClusterResultExport($cluster), $fileName . '.' . $type);

            Common::fileExport($file, $type, $fileName);
        }

        return abort(403);
    }

    public function clusterConfiguration(): View
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        return view('cluster.config', [
            'config' => ClusterConfiguration::first(),
            'config_classic' => ClusterConfigurationClassic::first(),
            'admin' => User::isUserAdmin()
        ]);
    }

    public function changeClusterConfiguration(Request $request): RedirectResponse
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        if ($request->input('type') === 'pro') {
            $config = ClusterConfiguration::first();
        } else {
            $config = ClusterConfigurationClassic::first();
        }

        $params = $request->all();
        unset($params['type']);

        $config->update($params);

        return Redirect::route('cluster.configuration');
    }

    public function downloadClusterSites(Request $request): JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->projectId)->first('default_result');
        $results = Common::uncompressArray($cluster->default_result);

        foreach ($results as $result) {
            if (key_exists($request->phrase, $result)) {
                return response()->json([
                    'sites' => $result[$request->phrase]['sites'],
                    'mark' => $result[$request->phrase]['mark'] ?? 0
                ]);
            }
        }

        return response()->json([], 404);
    }

    public function downloadClusterCompetitors(Request $request): JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->projectId)->first('default_result');
        $results = Common::uncompressArray($cluster->default_result);
        arsort($results[$request->key]['finallyResult']['sites']);

        return response()->json([
            'competitors' => $results[$request->key]['finallyResult']['sites']
        ]);
    }

    public function downloadClusterPhrases(Request $request): JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->projectId)->first('request');
        $phrases = json_decode($cluster->request, true)['phrases'];

        return response()->json([
            'phrases' => explode("\n", $phrases)
        ]);
    }

    public function editClusters(ClusterResults $cluster)
    {
        if ($cluster->created_at <= Carbon::parse('00:00 22.02.2023')) {
            return abort(403, __('In order to edit this result, you need to reshoot it'));
        }

        $cluster->request = json_decode($cluster->request, true);
        $clusters = Cluster::unpackCluster($cluster->result);

        if (isset($cluster->html) && $cluster->html !== '') {
            $ar = json_decode($cluster->html, true);
            $cluster->setClusters($cluster->result);
            $html = $cluster->parseTree($ar);

            return view('cluster.edit', [
                'cluster' => $cluster,
                'clusters' => $clusters,
                'html' => $html,
                'admin' => User::isUserAdmin()
            ]);
        }

        ksort($clusters);

        return view('cluster.edit', [
            'cluster' => $cluster,
            'clusters' => $clusters,
            'admin' => User::isUserAdmin()
        ]);
    }

    public function editCluster(Request $request): ?JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->input('id'))->first();
        if (User::isUserAdmin() || $cluster->user_id == Auth::id()) {
            $clusterItem = [];

            $cluster->result = Cluster::unpackCluster($cluster->result);
            $clusters = $cluster->result;
            foreach ($clusters as $mainPhrase => $items) {
                foreach ($items as $phrase => $item) {
                    if ($phrase === $request->input('phrase')) {
                        unset($item['merge']);
                        unset($clusters[$mainPhrase][$phrase]);
                        $clusters[$request->input('mainPhrase')][$request->input('phrase')] = $item;
                        $clusterItem = $item;
                    }
                }
            }

            Cluster::recalculateClusterInfo($cluster, $clusters);

            return response()->json([
                'success' => true,
                'countClusters' => $cluster->count_clusters,
                'based' => $clusterItem['based']['number'] ?? $clusterItem['based'],
                'phrased' => $clusterItem['phrased']['number'] ?? $clusterItem['phrased'],
                'target' => $clusterItem['target']['number'] ?? $clusterItem['target'],
            ]);

        }

        return abort(403);
    }

    public function checkGroupName(Request $request): JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->input('id'))->first();
        if (User::isUserAdmin() || $cluster->user_id == Auth::id()) {
            $cluster->result = Cluster::unpackCluster($cluster->result);
            $result = Cluster::isGroupNameExist($request->input('groupName'), $cluster->result);

            if ($result['error']) {
                return response()->json([
                    'message' => $result['message'],
                ], 400);
            }

            return response()->json([
                'result' => $result ?? false
            ]);
        }

        return response()->json([
            'success' => false,
        ], 400);

    }

    public function changeGroupName(Request $request): JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->input('id'))->first();
        if (User::isUserAdmin() || $cluster->user_id == Auth::id()) {
            $cluster->result = Cluster::unpackCluster($cluster->result);
            $keys = array_keys($cluster->result);

            if (in_array($request->input('newGroupName'), $keys)) {
                if (count($cluster->result[$request->input('newGroupName')]) > 2) {
                    return response()->json([
                        'success' => false,
                    ], 400);
                } else {
                    $item = $cluster->result[$request->input('newGroupName')][$request->input('newGroupName')];
                }

            }

            $clusters = $cluster->result;
            $clusters[$request->input('newGroupName')] = $clusters[$request->input('oldGroupName')];
            if (isset($item)) {
                $clusters[$request->input('newGroupName')][$request->input('newGroupName')] = $item;
            }
            unset($clusters[$request->input('oldGroupName')]);
            ksort($clusters);
            arsort($clusters);
            $cluster->result = base64_encode(gzcompress(json_encode($clusters), 9));
            $cluster->count_clusters = count($clusters);
            $cluster->save();

            return response()->json([
                'success' => true,
                'move' => isset($item)
            ]);
        }

        return response()->json([
            'success' => false,
        ], 400);
    }

    public function confirmationNewCluster(Request $request): ?JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->input('projectId'))->first();
        if (User::isUserAdmin() || $cluster->user_id == Auth::id()) {
            $clusters = Cluster::unpackCluster($cluster->result);
            foreach ($clusters as $mainPhrase => $items) {
                foreach ($items as $phrase => $item) {
                    if (in_array($phrase, $request->input('phrases')) && $request->input('mainPhrase') !== $mainPhrase) {
                        $clusters[$request->input('mainPhrase')][$phrase] = $item;
                        unset($clusters[$mainPhrase][$phrase]);
                    }
                }
            }

            Cluster::recalculateClusterInfo($cluster, $clusters);

            return response()->json([
                'success' => true,
                'groupId' => Str::random(10),
            ]);
        }

        return abort(403);

    }

    public function resetAllChanges(Request $request): ?JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->input('projectId'))->first();
        if (User::isUserAdmin() || $cluster->user_id == Auth::id()) {
            $cluster->result = $cluster->default_result;
            $cluster->html = null;
            $cluster->save();

            return response()->json([]);
        }

        return response()->json([], 403);
    }

    public function downloadClusterGroup(Request $request)
    {
        $cluster = ClusterResults::find($request->input('id'));
        $clusters = Cluster::unpackCluster($cluster->result);
        $array = json_decode($request->json, true);
        $file = Excel::download(new ClusterGroupExport($clusters, $array), "group_results.$request->type");

        Common::fileExport($file, $request->type, 'group_results');
    }

    public function saveTree(Request $request): JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->input('projectId'))->first();
        if (User::isUserAdmin() || $cluster->user_id == Auth::id()) {
            $cluster->html = $request->html;
            $cluster->save();

            return response()->json([]);
        }

        return response()->json([], 403);
    }

    public function setCleaningInterval(Request $request): RedirectResponse
    {
        ClusterConfiguration::where('id', '>', 0)->update([
            'cleaning_interval' => $request->input('cleaning_interval')
        ]);

        return Redirect::back();
    }
}
