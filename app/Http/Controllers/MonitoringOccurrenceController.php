<?php

namespace App\Http\Controllers;

use App\Classes\Services\XmlRiver;
use App\Jobs\OccurrenceQueue;
use App\MonitoringProject;
use Illuminate\Http\Request;

class MonitoringOccurrenceController extends Controller
{
    public function index()
    {
        $project = MonitoringProject::find(48);
        $keywords = $project->keywords;

        foreach ($keywords as $keyword){
            dispatch((new OccurrenceQueue($keyword, $project->searchengines[1]))->onQueue('high'));
        }
    }
}
