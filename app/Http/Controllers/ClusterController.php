<?php

namespace App\Http\Controllers;

use App\Cluster;
use App\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClusterController extends Controller
{
    /**
     * @param $results
     * @param string $phrases
     * @return View
     */
    public function index($results = null, string $phrases = ''): View
    {
        $admin = User::isUserAdmin();

        return view('cluster.index', ['admin' => $admin, 'results' => $results, 'phrases' => $phrases]);
    }

    /**
     * @param Request $request
     * @return View
     */
    public function analysisCluster(Request $request): View
    {
        $cluster = new Cluster($request->all());
        $cluster->startAnalysis();

        return $this->index($cluster->getAnalysisResult(), $request->input('phrases'));
    }
}
