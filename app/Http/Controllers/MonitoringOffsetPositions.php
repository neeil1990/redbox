<?php

namespace App\Http\Controllers;

use App\Monitoring\Positions\Offset;
use App\MonitoringProject;
use Illuminate\Http\Request;

class MonitoringOffsetPositions extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:Super Admin|admin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = (new MonitoringProject())->all();

        return view('monitoring.admin.offset_positions.index', compact('projects'));
    }

    public function offset(Request $request)
    {

    }


}
