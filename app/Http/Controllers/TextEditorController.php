<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectDescriptionRequest;
use App\Http\Requests\EditProjectDescriptionRequest;
use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\EditProjectRequest;
use App\Project;
use App\ProjectDescription;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;
use JavaScript;

class TextEditorController extends Controller
{
    /**
     * @return array|false|Application|Factory|RedirectResponse|View|mixed
     */
    public function index()
    {
        $projects = Project::where('user_id', Auth::user()->id)
            ->get()
            ->sortByDesc('id');
        if (count($projects) === 0) {
            return self::createView(false);
        }

        return view('project-and-descriptions.projects', compact('projects'));
    }

    /**
     * @param boolean $showButton
     * @return array|false|Application|Factory|RedirectResponse|View|mixed
     */
    public function createView(bool $showButton = true)
    {
        $user_id = Auth::id();
        if (self::isCountProjectsMoreTwenty($user_id)) {
            flash()->overlay(__('You have created the maximum number of projects(20), you need to delete something'), ' ')
                ->error();
            return Redirect::route('HTML.editor');
        }
        self::getLanguage();

        return view('project-and-descriptions.create-project', compact('showButton'));
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
     * @param EditProjectRequest $request
     * @return RedirectResponse
     */
    protected function editProject(EditProjectRequest $request): RedirectResponse
    {
        $project = Project::find($request->project_id);
        $project->update([
            'project_name' => $request->project_name,
            'short_description' => $request->short_description
        ]);
        flash()->overlay(__('Project was successfully changed'), ' ')
            ->success();

        return Redirect::route('HTML.editor');
    }

    /**
     * @param CreateProjectRequest $request
     * @return RedirectResponse
     */
    public function saveProject(CreateProjectRequest $request): RedirectResponse
    {
        if (self::isDescriptionEmpty($request->description)) {
            flash()->overlay(__('The text cannot be empty'), ' ')
                ->error();
            return Redirect::back();
        }
        $project = new Project();
        $project->project_name = $request->project_name;
        if (empty($request->short_description)) {
            $project->short_description = Str::limit(strip_tags($request->description), 70);
        } else {
            $project->short_description = $request->short_description;
        }
        $project->user_id = Auth::id();
        $project->save();
        self::saveDescription($request->description, $project->id);

        flash()->overlay(__('Project was successfully created'), $project->project_name)
            ->success();

        return Redirect::route('create.project');
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
        self::getLanguage();

        return view('project-and-descriptions.edit-description', compact('description'));
    }

    /**
     * @param EditProjectDescriptionRequest $request
     * @return array|Application|Factory|RedirectResponse|View|mixed
     */
    public function editDescription(EditProjectDescriptionRequest $request)
    {
        if (self::isDescriptionEmpty($request->description)) {
            flash()->overlay(__('The text cannot be empty'), ' ')
                ->error();

            return $this->editDescriptionView($request->description_id);
        }

        $description = ProjectDescription::where('id', $request->description_id)->first();
        $description->description = $request->description;
        $description->save();
        flash()->overlay(__('Text was successfully change'), ' ')
            ->success();

        return Redirect::route('HTML.editor');
    }

    /**
     * @param string $id
     * @return RedirectResponse
     */
    public function destroyDescription(string $id): RedirectResponse
    {
        ProjectDescription::destroy($id);
        flash()->overlay(__('Text was successfully deleted'), ' ')
            ->success();

        return Redirect::back();
    }

    /**
     * @return array|Application|Factory|View|mixed
     */
    public function createDescriptionView()
    {
        $user_id = Auth::id();
        if (self::isCountDescriptionProjectsMoreThirty($user_id)) {
            flash()->overlay(__('You have reached the maximum number of texts per project, you need to delete something'), ' ')
                ->error();

            return Redirect::route('HTML.editor');
        }

        $projects = Project::where('user_id', $user_id)->get();
        self::getLanguage();

        return view('project-and-descriptions.create-description')->with('projects', $projects);
    }

    /**
     * @param CreateProjectDescriptionRequest $request
     * @return RedirectResponse
     */
    public function createDescription(CreateProjectDescriptionRequest $request): RedirectResponse
    {
        if (self::isDescriptionEmpty($request->description)) {
            flash()->overlay(__('The text cannot be empty'), ' ')
                ->error();

            return Redirect::back();
        }

        self::saveDescription($request->description, $request->project_id);
        flash()->overlay(__('Text was saved successfully'), ' ')
            ->success();

        return Redirect::back();
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
     * @param $user_id
     * @return bool
     */
    public static function isCountDescriptionProjectsMoreThirty($user_id): bool
    {
        $descriptions = Project::where('user_id', $user_id)->withCount('descriptions')->get();
        foreach ($descriptions as $description) {
            if ($description->descriptions_count >= 30) {
                return true;
            }
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

    public static function getLanguage()
    {
        JavaScript::put([
            'language' => __('en-US'),
        ]);
    }

    /**
     * @param $description
     * @return bool
     */
    public static function isDescriptionEmpty($description): bool
    {
        if (strip_tags($description) === "") {
            return true;
        }
        return false;
    }
}
