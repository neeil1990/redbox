<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectDescriptionRequest;
use App\Http\Requests\ProjectDescriptionRequest;
use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Project;
use App\ProjectDescription;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TextEditorController extends Controller
{
    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function index()
    {
        $projects = Project::where('user_id', Auth::user()->id)
            ->get()
            ->sortByDesc('id');

        return view('project-and-descriptions.projects', compact('projects'));
    }

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function createView()
    {
        if (self::isCountProjectsMoreTwenty(Auth::id())) {
            flash()->overlay(__('You have created the maximum number of projects(20), you need to delete something'), ' ')
                ->error();
            return Redirect::route('projects');
        }

        return view('project-and-descriptions.create-project');
    }

    /**
     * @param string $id
     * @return array|false|Application|Factory|View|mixed
     */
    public function editProjectView(string $id)
    {
        $project = Project::where('id', $id)->first();
        return view('project-and-descriptions.edit-project', compact('project'));
    }

    /**
     * @param UpdateProjectRequest $request
     * @return RedirectResponse
     */
    protected function editProject(UpdateProjectRequest $request): RedirectResponse
    {
        $project = Project::find($request->project_id);
        $project->update([
            'project_name' => $request->project_name,
            'short_description' => $request->short_description
        ]);

        flash()->overlay(__('Project was successfully changed'), ' ')
            ->success();
        return Redirect::route('projects');
    }

    /**
     * @param CreateProjectRequest $request
     * @return RedirectResponse
     */
    public function saveProject(CreateProjectRequest $request): RedirectResponse
    {
        $project = new Project();
        $project->project_name = $request->project_name;
        if (empty($request->short_description)) {
            $project->short_description = Str::limit(strip_tags($request->description), '100');
        } else {
            $project->short_description = $request->short_description;
        }
        $project->user_id = Auth::id();
        $project->save();
        self::saveDescription($request->description, $project->id);

        flash()->overlay(__('Project was successfully created'), ' ')
            ->success();
        return Redirect::route('projects');
    }

    /**
     * @param string $id
     * @return RedirectResponse
     */
    public function destroyProject(string $id): RedirectResponse
    {
        Project::destroy($id);
        flash()->overlay(__('Project was successfully deleted'), ' ')
            ->success();
        return Redirect::back();
    }

    /**
     * @param string $id
     * @return array|Application|Factory|View|mixed
     */
    public function editDescriptionView(string $id)
    {
        $description = ProjectDescription::where('id', $id)->first();
        return view('project-and-descriptions.edit-description', compact('description'));
    }

    /**
     * @param ProjectDescriptionRequest $request
     * @return array|Application|Factory|RedirectResponse|View|mixed
     */
    public function editDescription(ProjectDescriptionRequest $request)
    {
        if (strlen(strip_tags($request->description)) == 0) {
            flash()->overlay(__('The description cannot be empty'), ' ')
                ->error();
            return $this->editDescriptionView($request->description_id);
        }
        $description = ProjectDescription::where('id', $request->description_id)->first();
        $description->description = $request->description;
        $description->save();
        flash()->overlay(__('Description was successfully change'), ' ')
            ->success();

        return Redirect::route('projects');
    }

    /**
     * @param string $id
     * @return RedirectResponse
     */
    public function destroyDescription(string $id): RedirectResponse
    {
        ProjectDescription::destroy($id);
        flash()->overlay(__('description was successfully deleted'), ' ')
            ->success();
        return Redirect::back();
    }

    /**
     * @return array|Application|Factory|View|mixed
     */
    public function createDescriptionView()
    {
        $projects = Project::where('user_id', Auth::id())->get();
        return view('project-and-descriptions.create-description')->with('projects', $projects);
    }

    /**
     * @param CreateProjectDescriptionRequest $request
     * @return RedirectResponse
     */
    public function createDescription(CreateProjectDescriptionRequest $request): RedirectResponse
    {
        self::saveDescription($request->description, $request->project_id);
        flash()->overlay(__('Description was saved successfully'), ' ')
            ->success();
        return Redirect::route('projects');
    }

    /**
     * @param $userId
     * @return bool
     */
    public static function isCountProjectsMoreTwenty($userId): bool
    {
        if (Project::where('user_id', $userId)->count() >= 20) {
            return true;
        }
        return false;
    }

    /**
     * @param $description
     * @param $id
     */
    public static function saveDescription($description, $id)
    {
        $projectDescription = new ProjectDescription();
        $projectDescription->description = $description;
        $projectDescription->project_id = $id;
        $projectDescription->save();
    }
}
