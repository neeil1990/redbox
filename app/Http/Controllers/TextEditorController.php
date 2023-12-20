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

    public function __construct()
    {
        $this->middleware(['permission:Html editor']);
    }

    /**
     * @return array|false|Application|Factory|RedirectResponse|View|mixed
     */
    public function index()
    {
        $projects = Project::where('user_id', Auth::id())->get()->sortByDesc('id');
        if (count($projects) === 0) {
            return self::createView(false);
        }

        return view('html-editor.projects', compact('projects'));
    }

    /**
     * @param boolean $showButton
     * @return array|false|Application|Factory|RedirectResponse|View|mixed
     */
    public function createView(bool $showButton = true)
    {
        $user = Auth::user();
        if (self::isCountProjectsMoreTwenty($user->id)) {
            flash()->overlay(__('You have created the maximum number of projects(20), you need to delete something'), ' ')
                ->error();
            return Redirect::route('HTML.editor');
        }

        $lang = Auth::user()->lang;

        return view('html-editor.create-project', compact('showButton', 'lang'));
    }

    /**
     * @param int $id
     * @return array|false|Application|Factory|View|mixed
     */
    public function editProjectView(int $id)
    {
        $project = Project::findOrFail($id);

        return view('html-editor.edit-project', compact('project'));
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
     * @return array|false|Application|Factory|RedirectResponse|View|mixed
     */
    public function storeProject(CreateProjectRequest $request)
    {
        if (self::isDescriptionEmpty($request->description)) {
            flash()->overlay(__('The text cannot be empty'), ' ')->error();

            return Redirect::back();
        }
        $showButton = true;
        ProjectDescription::storeDescriptionProject($request->description, Project::createNewProject($request));

        flash()->overlay(__('Project was successfully created'), $request->project_name)->success();

        return view('html-editor.create-project', compact('showButton', 'request'));
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
        $lang = Auth::user()->lang;
        $project = ProjectDescription::where('id', $id)->first();

        return view('html-editor.edit-description', compact('project', 'lang'));
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
        $user = Auth::user();
        $lang = $user->lang;

        if (self::isCountDescriptionProjectsMoreThirty($user->id)) {
            flash()->overlay(__('You have reached the maximum number of texts per project, you need to delete something'), ' ')
                ->error();

            return Redirect::route('HTML.editor');
        }

        $projects = Project::where('user_id', $user->id)->get();

        return view('html-editor.create-description', compact('lang'))->with('projects', $projects);
    }

    /**
     * @param CreateProjectDescriptionRequest $request
     * @return RedirectResponse
     */
    public function createDescription(CreateProjectDescriptionRequest $request): RedirectResponse
    {
        if (self::isDescriptionEmpty($request->description)) {
            flash()->overlay(__('The text cannot be empty'), ' ')->error();

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
        $projectDescription = new ProjectDescription([
            'description' => $description,
            'project_id' => $id,
        ]);

        $projectDescription->save();
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
