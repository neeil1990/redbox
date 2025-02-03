<?php

namespace App\Http\Controllers;

use App\Events\MonitoringPositionInsert;
use App\Monitoring\FillEmptyPositions;
use App\MonitoringProject;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class MonitoringSetPositions extends Controller
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

        return view('monitoring.admin.set_positions.index', compact('projects'));
    }

    public function projectSearchEngines(Request $request)
    {
        $id = $request->get('id');

        if (!$id) {
            return false;
        }

        $project = MonitoringProject::findOrFail($id);

        return $project->searchengines;
    }

    public function insertPositions(Request $request)
    {
        $projectId = $request->input('projectId');
        $engineId = $request->input('engineId');

        $fillPositions = new FillEmptyPositions($projectId, $engineId, $request->input('startDate'), $request->input('endDate'));

        $fillPositions->execute();
    }
}
