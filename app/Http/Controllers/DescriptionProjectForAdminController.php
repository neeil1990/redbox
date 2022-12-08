<?php

namespace App\Http\Controllers;

use App\MainProject;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class DescriptionProjectForAdminController extends Controller
{
    protected $roles;

    public function __construct()
    {
        $this->middleware(['permission:Main projects']);

        $this->roles = Role::all()->pluck('name', 'name');
    }

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function index()
    {
        $data = MainProject::all();
        return view('main-projects.index', compact('data'));
    }

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function create()
    {
        $roles = $this->roles;
        return view('main-projects.create', compact('roles'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        MainProject::create($request->all());
        return redirect()->route('main-projects.index');
    }

    /**
     * @param $id
     * @return array|false|Application|Factory|View|mixed
     */
    public function edit($id)
    {
        $data = MainProject::find($id);
        $roles = $this->roles;
        return view('main-projects.edit', compact('data', 'roles'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        MainProject::find($id)->update([
            'access' => $request->access,
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'icon' => $request->icon,
            'show' => $request->show === 'on'
        ]);

        return redirect()->route('main-projects.index');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        MainProject::where('id', $id)->delete();
        return redirect()->route('main-projects.index');
    }
}
