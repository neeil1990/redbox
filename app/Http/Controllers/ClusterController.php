<?php

namespace App\Http\Controllers;

use App\Cluster;
use App\ClusterProgress;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClusterController extends Controller
{
    /**
     * @param $result
     * @return View
     */
    public function index($result = null): View
    {
        $admin = User::isUserAdmin();

        return view('cluster.index', ['admin' => $admin, 'results' => $result]);
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
}
