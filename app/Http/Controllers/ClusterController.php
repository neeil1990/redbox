<?php

namespace App\Http\Controllers;

use App\Cluster;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ClusterController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $admin = User::isUserAdmin();

        $sessionResult = $request->session()->pull('cluster.results');
        if (isset($sessionResult)) {
            dd($sessionResult);
            $sessionResult = $sessionResult[0];
            $request->session()->pull('cluster.results');
        } else {
            $sessionResult = null;
        }

        return view('cluster.index', ['admin' => $admin, 'results' => $sessionResult]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function analysisCluster(Request $request): RedirectResponse
    {
        $cluster = new Cluster($request->all());
        $cluster->startAnalysis();
        $request->session()->push('cluster.results', $cluster->getAnalysisResult());

        return Redirect::back()->withInput();
    }
}
