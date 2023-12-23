<?php

namespace App\Http\Controllers;

use App\Classes\Monitoring\PanelButtons\SimpleButtonsFactory;
use App\MonitoringProject;
use App\SearchIndex;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringTopController extends Controller
{
    private function navigations(MonitoringProject $project): array
    {
        /** @var User $user */
        $user = Auth::user();
        $buttons = new SimpleButtonsFactory();

        return $buttons->createButtons($user, $project);
    }

    public function index(MonitoringProject $project)
    {
        $navigations = $this->navigations($project);

        return view('monitoring.top100.index', ['project' => $project, 'navigations' => $navigations]);
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
