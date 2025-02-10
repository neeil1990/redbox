<?php

namespace App\Http\Controllers;

use App\Monitoring\Positions;
use App\MonitoringProject;
use Illuminate\Http\Request;

class MonitoringSetPositions extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:Super Admin|admin']);
    }

    public function index()
    {
        $projects = (new MonitoringProject())->all();

        return view('monitoring.admin.set_positions.index', compact('projects'));
    }

    public function insertPositions(Request $request)
    {
        (new Positions\Fill(
            $request->input('projectId'),
            $request->input('engineId'),
            $request->input('startDate'),
            $request->input('endDate')
        ))->execute();
    }
}
