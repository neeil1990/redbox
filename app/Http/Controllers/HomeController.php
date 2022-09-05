<?php

namespace App\Http\Controllers;

use App\MainProject;
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
        $user_id = Auth::id();
        $projectsPositions = ProjectsPositions::where('user_id', '=', $user_id)->get('projects_positions');
        if (empty($projectsPositions[0])) {
            $result = MainProject::all()->toArray();
        } else {
            $projectsPositions = explode(',', substr($projectsPositions[0]->projects_positions, 0, -1));
            $projects = MainProject::all()->toArray();
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
    public function projectSort(Request $request)
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

        return response('success');
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function menuItemSort(Request $request)
    {
        $projectsPositions = ProjectsPositions::firstOrNew([
            'user_id' => Auth::id(),
        ]);

        Log::debug('orders', [$request->all()]);

        $positions = '';
        foreach ($request->positions as $key => $position) {
            $positions .= $position . ',';
        }

        $projectsPositions->menu_positions = $positions;

        $projectsPositions->save();

        return response('success');
    }

    /**
     * @return Application|ResponseFactory|Response
     */
    public function getDescriptionProjects()
    {
        $response = [];

        $user = Auth::user();
        $projectsPositions = ProjectsPositions::where('user_id', '=', $user->id)->get('menu_positions')->toArray();
        if (empty($projectsPositions[0]['menu_positions'])) {
            $result = MainProject::all()->toArray();
        } else {
            $projectsPositions = explode(',', substr($projectsPositions[0]['menu_positions'], 0, -1));
            $projects = MainProject::all()->toArray();
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

        foreach ($result as $item) {

            $access = (is_null($item['access'])) ? [] : $item['access'];

            if ($user->hasRole($access))
                $response[] = [
                    'id' => $item['id'],
                    'title' => __($item['title']),
                    'description' => $item['description'],
                    'link' => $item['link'],
                    'icon' => $item['icon'],
                ];
        }

        return response($response);
    }
}
