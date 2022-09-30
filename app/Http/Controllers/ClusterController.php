<?php

namespace App\Http\Controllers;

use App\Cluster;
use App\CompetitorConfig;
use App\Relevance;
use App\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClusterController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        $admin = User::isUserAdmin();

        return view('cluster.index', ['admin' => $admin]);
    }

    public function analysisCluster(Request $request)
    {
        $cluster = new Cluster($request->all());
        $result = $cluster->startAnalysis();
        $admin = User::isUserAdmin();

        return view('cluster.index', ['admin' => $admin, 'result' => $result]);
    }
}
