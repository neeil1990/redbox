<?php

namespace App\Http\Controllers;

use App\DescriptionProject;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:user');
    }

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function index()
    {
        $posts = DescriptionProject::orderBy('order', 'ASC')->get();

        return view('home', compact('posts'));
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function updateOrder(Request $request)
    {
        $posts = DescriptionProject::all();

        foreach ($posts as $post) {
            foreach ($request->order as $order) {
                if ($order['id'] == $post->id) {
                    $post->update(['order' => $order['position']]);
                }
            }
        }

        return response('success');
    }
}
