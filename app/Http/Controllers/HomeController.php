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
        try {
            $projectsPositions = ProjectsPositions::where('user_id', '=', Auth::id())->get('menu_positions')->toArray();
            if (empty($projectsPositions[0]['menu_positions'])) {
                $result = DescriptionProject::all(['id', 'title', 'description', 'link'])->toArray();
                for ($i = 0; $i < count($result); $i++) {
                    $result[$i]['title'] = __($result[$i]['title']);
                }
            } else {
                $projectsPositions = explode(',', substr($projectsPositions[0]['menu_positions'], 0, -1));
                $projects = DescriptionProject::all(['id', 'title', 'description', 'link'])->toArray();
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
                for ($i = 0; $i < count($result); $i++) {
                    Log::debug('af un', [$result[$i]['title']]);
                    $result[$i]['title'] = __($result[$i]['title']);
                }
            }

            return response((array)$result);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }

        $user_id = Auth::id();
        $projectsPositions = ProjectsPositions::where('user_id', '=', $user_id)->get('projects_positions');
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
        $positions = '';
        foreach ($request->orders as $order) {
            $positions .= $order['id'] . ',';
        }
        Log::debug('sort', [$positions]);
        $projectsPositions = ProjectsPositions::firstOrNew([
            'user_id' => Auth::id(),
        ]);
        $projectsPositions->menu_positions = $positions;

        $projectsPositions->save();

        return response('success');
    }

    /**
     * @return Application|ResponseFactory|Response
     */
    public function getDescriptionProjects()
    {
        $projectsPositions = ProjectsPositions::where('user_id', '=', Auth::id())->get('menu_positions')->toArray();
        if (empty($projectsPositions[0]['menu_positions'])) {
            $result = DescriptionProject::all(['id', 'title', 'description', 'link'])->toArray();
            for ($i = 0; $i < count($result); $i++) {
                $result[$i]['title'] = __($result[$i]['title']);
            }
        } else {
            $projectsPositions = explode(',', substr($projectsPositions[0]['menu_positions'], 0, -1));
            $projects = DescriptionProject::all(['id', 'title', 'description', 'link'])->toArray();
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
            for ($i = 0; $i < count($result); $i++) {
                Log::debug('af un', [$result[$i]['title']]);
                $result[$i]['title'] = __($result[$i]['title']);
            }
        }

        return response((array)$result);
    }
}
