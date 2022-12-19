<?php

namespace App\Http\Controllers;

use App\MainProject;
use App\ProjectsPositions;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class HomeController extends Controller
{

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function index()
    {
        $result = $this->getProjects();

        return view('home', compact('result'));
    }

    protected function getProjects(): array
    {
        if (User::isUserAdmin()) {
            $result = MainProject::all()->toArray();
        } else {
            $result = MainProject::where('show', '=', 1)->get()->toArray();
        }

        return $result;
    }

}
