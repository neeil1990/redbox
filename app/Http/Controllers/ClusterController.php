<?php

namespace App\Http\Controllers;

use App\Cluster;
use App\User;
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
     * @return View
     */
    public function analysisCluster(Request $request): View
    {
        $cluster = new Cluster($request->all());
        $cluster->startAnalysis();
        $cluster->getAnalysisResult();

        return $this->index($cluster->getAnalysisResult());
    }
}
