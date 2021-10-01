<?php

namespace App\Http\Controllers;

use App\DescriptionProject;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
        $data = DescriptionProject::all();
        return view('main-projects.index', compact(['data']));
    }

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function create()
    {
        return view('main-projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'link' => 'required'
        ]);

        DescriptionProject::create($request->all());
        return Redirect::refresh();
    }

    /**
     * @param $id
     * @return array|false|Application|Factory|View|mixed
     */
    public function edit($id)
    {
        $data = DescriptionProject::find($id);
        return view('main-projects.edit', compact(['data']));
    }

    /**
     * @param Request $request
     * @param $id
     * @return array|false|Application|Factory|View|mixed
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'link' => 'required'
        ]);

        DescriptionProject::where('id', $id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link
        ]);

        return self::index();

    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        DescriptionProject::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Delete Successfully');
    }
}
