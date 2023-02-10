<?php

namespace App\Http\Controllers;

use App\Cluster;
use App\ClusterConfiguration;
use App\ClusterConfigurationClassic;
use App\ClusterQueue;
use App\ClusterResults;
use App\Common;
use App\Exports\ClusterResultExport;
use App\Jobs\Cluster\StartClusterAnalyseQueue;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'totalPhrases' => count(array_unique(array_diff(explode("\n", str_replace("\r", "", $request['phrases'])), [])))
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

        $count = ClusterQueue::where('progress_id', '=', $id)->count();
        return response()->json([
            'count' => $count,
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

        $count = ClusterQueue::where('progress_id', '=', $id)->count();
        return response()->json([
            'count' => $count,
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

    public function showResult(ClusterResults $cluster): View
    {
        if ($cluster->user_id !== Auth::id() && !User::isUserAdmin() || $cluster->show === 0) {
            return abort(403);
        }

        $cluster->result = gzuncompress(base64_decode($cluster->result));
        $cluster->request = json_decode($cluster->request, true);

        return view('cluster.show', ['cluster' => $cluster->toArray(), 'admin' => User::isUserAdmin()]);
    }


    public function setClusterRelevanceUrl(Request $request): JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->input('projectId'))
            ->where('user_id', '=', Auth::id())
            ->first();

        if (isset($cluster)) {
            $cluster->result = Cluster::unpackCluster($cluster->result);
            $results = $cluster->result;

            foreach ($results as $key => $items) {
                foreach ($items as $phrase => $item) {
                    if ($phrase === $request->input('phrase')) {
                        $results[$key][$phrase]['link'] = $request->input('url');
                        unset($results[$key][$phrase]['relevance']);
                    }
                }
            }

            $cluster->result = base64_encode(gzcompress(json_encode($results), 9));
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
            $cluster->result = Cluster::unpackCluster($cluster->result);
            $results = $cluster->result;

            foreach ($results as $key => $items) {
                foreach ($items as $phrase => $item) {
                    if (in_array($phrase, $request->input('phrases'))) {
                        $results[$key][$phrase]['link'] = $request->input('url');
                        unset($results[$key][$phrase]['relevance']);
                    }
                }
            }

            $cluster->result = base64_encode(gzcompress(json_encode($results), 9));
            $cluster->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }

    public function downloadClusterResult(ClusterResults $cluster, string $type)
    {
        if (!User::isUserAdmin() && $cluster->user_id !== Auth::id() || !($type === 'xls' || $type === 'csv')) {
            return abort(403);
        }

        if (isset($cluster->domain)) {
            $domain = str_replace(['https://', 'http://'], '', $cluster->domain);
            $fileName = Carbon::parse($cluster->created_at)->toDateString() . '-' . str_replace(['.', '/', ' '], '-', $domain);
        } else {
            $fileName = Carbon::parse($cluster->created_at)->toDateString() . '-' . $cluster->id;
        }

        $file = Excel::download(new ClusterResultExport($cluster), $fileName . '.' . $type);
        Common::fileExport($file, $type, $fileName);
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
        $cluster = ClusterResults::where('id', '=', $request->projectId)->first('result');
        $results = Common::uncompressArray($cluster->result);

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
        $cluster = ClusterResults::where('id', '=', $request->projectId)->first('result');
        $results = Common::uncompressArray($cluster->result);
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
        if ($cluster->created_at <= Carbon::parse('00:00 30.01.2023')) {
            return abort(403, __('In order to edit this result, you need to reshoot it'));
        }

        $clusters = Cluster::unpackCluster($cluster->result);
        $cluster->request = json_decode($cluster->request, true);

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
        if (!User::isUserAdmin() && $cluster->user_id !== Auth::id()) {
            return abort(403);
        }

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

    public function checkGroupName(Request $request): JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->input('id'))->first();
        if ((!User::isUserAdmin() && $cluster->user_id !== Auth::id()) || preg_match("/[0-9]/", $request->input('groupName'))) {
            return response()->json([
                'success' => false,
            ], 400);
        }

        $cluster->result = Cluster::unpackCluster($cluster->result);
        $keys = array_keys($cluster->result);

        if (in_array($request->input('groupName'), $keys)) {
            return response()->json([
                'success' => false,
            ], 400);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function changeGroupName(Request $request): JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->input('id'))->first();
        if ((!User::isUserAdmin() && $cluster->user_id !== Auth::id()) || preg_match("/[0-9]/", $request->input('newGroupName'))) {
            return response()->json([
                'success' => false,
            ], 400);
        }

        $cluster->result = Cluster::unpackCluster($cluster->result);
        $keys = array_keys($cluster->result);

        if (in_array($request->input('newGroupName'), $keys)) {
            return response()->json([
                'success' => false,
            ], 400);
        }

        $clusters = $cluster->result;
        $clusters[$request->input('newGroupName')] = $clusters[$request->input('oldGroupName')];
        unset($clusters[$request->input('oldGroupName')]);
        ksort($clusters);
        arsort($clusters);
        $cluster->result = base64_encode(gzcompress(json_encode($clusters), 9));

        $cluster->save();

        return response()->json([
            'success' => true
        ]);
    }

    public function confirmationNewCluster(Request $request): ?JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->input('projectId'))->first();
        if (!User::isUserAdmin() && $cluster->user_id !== Auth::id()) {
            return abort(403);
        }

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

    public function resetAllChanges(Request $request): ?JsonResponse
    {
        $cluster = ClusterResults::where('id', '=', $request->input('projectId'))->first();
        if (!User::isUserAdmin() && $cluster->user_id !== Auth::id()) {
            return response()->json([], 403);
        }

        $cluster->result = $cluster->default_result;
        $cluster->save();

        return response()->json([]);
    }
}
