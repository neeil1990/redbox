<?php

namespace App\Http\Controllers;

use App\MainProject;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class MainProjectsController extends Controller
{
    protected $roles;

    public function __construct()
    {
        $this->middleware(['permission:Main projects']);

        $this->roles = Role::all()->pluck('name', 'name');
    }

    public function index()
    {
        $data = MainProject::orderBy('position', 'asc')->get();
        return view('main-projects.index', compact('data'));
    }

    public function create()
    {
        $roles = $this->roles;
        return view('main-projects.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'title' => ['required'],
            'position' => ['required', 'unique:main_projects'],
            'link' => ['required'],
            'icon' => ['required'],
        ], [
            'position.unique' => 'Такая позиция уже существует',
        ]);

        $request = $request->all();
        $request['show'] = $request['show'] === 'on';

        MainProject::create($request);
        return redirect()->route('main-projects.index');
    }

    public function edit($id)
    {
        $data = MainProject::find($id);
        $roles = $this->roles;
        return view('main-projects.edit', compact('data', 'roles'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $project = MainProject::find($id);
        $this->validate($request, [
            'title' => ['required'],
            'position' => Rule::unique('main_projects')->ignore($project->position, 'position'),
            'link' => ['required'],
            'icon' => ['required'],
        ], [
            'position.unique' => 'Такая позиция уже существует',
        ]);

        $request = $request->all();

        $request['show'] = isset($request['show']);
        $project->update($request);

        return redirect()->route('main-projects.index');
    }

    public function destroy($id): RedirectResponse
    {
        MainProject::where('id', $id)->delete();
        return redirect()->route('main-projects.index');
    }
}
