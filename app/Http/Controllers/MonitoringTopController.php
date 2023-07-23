<?php

namespace App\Http\Controllers;

use App\MonitoringProject;
use App\SearchIndex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MonitoringTopController extends Controller
{
    public function index(MonitoringProject $project)
    {
        return view('monitoring.top100.index', ['project' => $project]);
    }

    public function getTopSites(Request $request)
    {
        return SearchIndex::select('position', 'url', 'created_at')
            ->whereDate('created_at', $request->date)
            ->where('lr', $request->region)
            ->where('query', $request->word)
            ->orderBy('search_indices.id', 'desc')
            ->take(100)
            ->get();
    }
}
