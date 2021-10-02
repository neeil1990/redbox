<?php

namespace App\Http\Controllers;

use App\DescriptionProject;
use App\ProjectsPositions;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class HomeController extends Controller
{

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function index()
    {
        $projectsPositions = ProjectsPositions::where('user_id', '=', Auth::id())->get('projects_positions');
        if (empty($projectsPositions[0])) {
            $result = DescriptionProject::all()->toArray();
        } else {
            $projectsPositions = explode(',', substr($projectsPositions[0]->projects_positions, 0, -1));
            $projects = DescriptionProject::all()->toArray();
            $result = [];

            foreach ($projectsPositions as $projectsPosition) {
                foreach ($projects as $project) {
                    if ($project['id'] === (integer)$projectsPosition) {
                        $result[] = $project;
                    }
                }
            }
            $result = array_merge($result, $projects);
            $result = array_unique($result, SORT_REGULAR);
        }

        return view('home', compact('result'));
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function updateOrder(Request $request)
    {
        $positions = '';
        foreach ($request->orders as $order) {
            $positions .= $order['id'] . ',';
        }
        $projectsPositions = ProjectsPositions::firstOrNew([
            'user_id' => Auth::id(),
        ]);
        $projectsPositions->projects_positions = $positions;

        $projectsPositions->save();
        Log::debug('pst', [$positions]);
        Log::debug('pst2', [$projectsPositions]);

        return response('success');
    }
}
