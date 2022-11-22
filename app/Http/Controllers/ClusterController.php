<?php

namespace App\Http\Controllers;

use App\Cluster;
use App\ClusterConfiguration;
use App\ClusterProgress;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ClusterController extends Controller
{
    /**
     * @param $result
     * @return View
     */
    public function index($result = null): View
    {
        $admin = User::isUserAdmin();

        return view('cluster.index', ['admin' => $admin, 'results' => $result, 'config' => ClusterConfiguration::first()]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function analyseCluster(Request $request): JsonResponse
    {
        Log::debug('кластер старт');
        //TODO подключить гугл, переписать супервизор, затестить.
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
            'result' => true
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function startProgress(): JsonResponse
    {
        $progress = new ClusterProgress();
        $progress->save();

        return response()->json([
            'id' => $progress->id
        ], 201);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function getProgress(int $id): JsonResponse
    {
        $cluster = ClusterResults::where('progress_id', '=', $id)->first();
        if (isset($cluster)) {
            return response()->json([
                'percent' => 100,
                'result' => json_decode(gzuncompress(base64_decode($cluster->result)), true),
            ]);
        }

        $progress = ClusterProgress::where('id', '=', $id)->first();
        return response()->json([
            'percent' => $progress->percent,
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function getProgressModify(int $id): JsonResponse
    {
        $cluster = ClusterResults::where('progress_id', '=', $id)->first();
        if (isset($cluster)) {
            $cluster->request = json_decode($cluster->request, true);
            $cluster->region = Cluster::getRegionName($cluster->request['region']);
            return response()->json([
                'percent' => 100,
                'cluster' => $cluster,
            ]);
        }

        $progress = ClusterProgress::where('id', '=', $id)->first();
        return response()->json([
            'percent' => $progress->percent,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function fastScanClusters(Request $request): JsonResponse
    {
        $user = Auth::user();
        $cluster = new Cluster($request->all(), $user, false);
        $results = ClusterResults::findOrFail($request->input('resultId'));
        $cluster->setSites($results->sites_json);
        $cluster->searchClusters();
        $cluster->calculateClustersInfo();
        $clusters = collect($cluster->getClusters())->sortByDesc(function ($item, $key) {
            return count($item);
        })->values()->all();

        return response()->json([
            'sites' => $clusters,
            'count' => count($clusters)
        ]);
    }

    /**
     * @return View
     */
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
            $project->region = Cluster::getRegionName($request['region']);
            $project->request = $request;
        }

        return view('cluster.projects', ['projects' => $projects, 'admin' => $admin, 'config' => ClusterConfiguration::first()]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {
        ClusterResults::where('id', $request->id)
            ->where('user_id', '=', Auth::id())
            ->update([$request->option => $request->value]);
        return response()->json([]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
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

    /**
     * @param ClusterResults $cluster
     * @return View
     */
    public function showResult(ClusterResults $cluster): View
    {
        if ($cluster->user_id !== Auth::id() && !User::isUserAdmin()) {
            return abort(403);
        }

        $cluster->result = gzuncompress(base64_decode($cluster->result));
        $cluster->request = json_decode($cluster->request, true);

        return view('cluster.show', ['cluster' => $cluster->toArray(), 'admin' => User::isUserAdmin()]);
    }

    /**
     * @param ClusterResults $cluster
     * @param string $type
     * @return void|null
     */
    public function downloadClusterResult(ClusterResults $cluster, string $type)
    {
        if ($cluster->user_id !== Auth::id() || !($type === 'xls' || $type === 'csv')) {
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

    /**
     * @return View
     */
    public function clusterConfiguration(): View
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        $config = ClusterConfiguration::first();

        return view('cluster.config', ['config' => $config, 'admin' => User::isUserAdmin()]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function changeClusterConfiguration(Request $request): RedirectResponse
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        $config = ClusterConfiguration::first();

        $config->region = $request->input('region');
        $config->count = $request->input('count');
        $config->clustering_level = $request->input('clustering_level');
        $config->engine_version = $request->input('engine_version');
        $config->send_message = $request->input('sendMessage');
        $config->save_results = $request->input('save');
        $config->search_phrased = $request->input('searchPhrases') === 'on';
        $config->search_target = $request->input('searchTarget') === 'on';
        $config->save();

        return Redirect::route('cluster.configuration');
    }

}
