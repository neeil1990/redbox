<?php

namespace App\Http\Controllers;

use App\Cluster;
use App\ClusterConfiguration;
use App\ClusterProgress;
use App\ClusterResults;
use App\Common;
use App\Exports\ClusterResultExport;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
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
     */
    public function analysisCluster(Request $request): JsonResponse
    {
        $cluster = new Cluster($request->all());
        $cluster->startAnalysis();

        return response()->json([
            'result' => $cluster->getAnalysisResult()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatAnalysisCluster(Request $request): JsonResponse
    {
        $cluster = new Cluster($request->all());
        $cluster->startAnalysis();
        $result = $cluster->getNewCluster();
        $result->region = Cluster::getRegionName($request->input('region'));

        return response()->json([
            'cluster' => $result->toArray()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatAnalysis(Request $request): JsonResponse
    {
        $cluster = new Cluster($request->all());
        $cluster->startAnalysis();

        return response()->json([
            'result' => $cluster->getAnalysisResult()
        ], 200);
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
     * @param ClusterProgress $progress
     * @return JsonResponse
     */
    public function getProgress(ClusterProgress $progress): JsonResponse
    {
        return response()->json([
            'percent' => $progress->percent,
        ]);
    }

    /**
     * @return View
     */
    public function clusterProjects(): View
    {
        $admin = User::isUserAdmin();
        $projects = ClusterResults::where('user_id', '=', Auth::id())->get([
            'id', 'user_id', 'comment', 'domain', 'count_phrases', 'count_clusters', 'clustering_level',
            'top', 'created_at', 'request'
        ]);

        foreach ($projects as $key => $project) {
            $project->region = Cluster::getRegionName(json_decode($project->request, true)['region']);
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
