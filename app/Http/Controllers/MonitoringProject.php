<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MonitoringProject extends Controller
{
    public function getSearchEngines(Request $request)
    {
        $project = \App\MonitoringProject::findOrFail($request['id']);

        return $project->searchengines;
    }
}
