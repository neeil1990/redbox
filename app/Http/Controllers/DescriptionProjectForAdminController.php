<?php

namespace App\Http\Controllers;

use App\MainProject;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DescriptionProjectForAdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:admin');
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
        return view('main-projects.create');
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
        return view('main-projects.edit', compact('data'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        MainProject::where('id', $id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'icon' => $request->icon,
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
