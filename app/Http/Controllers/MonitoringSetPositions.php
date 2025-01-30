<?php

namespace App\Http\Controllers;

use App\Events\MonitoringPositionInsert;
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

        $endDate = Carbon::parse($request->input('endDate'));
        $startDate = Carbon::parse($request->input('startDate'));

        $period = CarbonPeriod::create($startDate, '1 day', $endDate);

        $maxPos = 20;

        $project = MonitoringProject::findOrFail($projectId);

        foreach ($project->keywords as $keyword) {

            $minPos = $keyword->positions()
                ->where('monitoring_searchengine_id', $engineId)
                ->whereDate('created_at', '<', $startDate)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->min('position');

            if ($minPos > $maxPos) {
                $maxPos = $minPos;
                $minPos -= 10;
            }

            foreach ($period as $date) {

                $dateFormat = $date->format('Y-m-d');

                $positionDoesntExist = $keyword->positions()
                    ->where('monitoring_searchengine_id', $engineId)
                    ->whereDate('created_at', $dateFormat)
                    ->doesntExist();

                if ($positionDoesntExist) {
                    $positions = $keyword->positions()->make([
                        'monitoring_searchengine_id' => $engineId,
                        'position' => rand($minPos, $maxPos),
                        'created_at' => $dateFormat,
                        'updated_at' => $dateFormat,
                    ])->load('keyword');

                    broadcast(new MonitoringPositionInsert($positions));
                }
            }
        }
    }
}
