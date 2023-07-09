<?php

namespace App\Http\Controllers;

use App\ClickTracking;
use App\MainProject;
use App\ProjectsPositions;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public function clickTracking(Request $request): JsonResponse
    {
        try {
            ClickTracking::updateOrCreate([
                'project_id' => $request->project_id,
                'button_text' => $request->button_text,
                'url' => $request->url,
                'user_id' => Auth::id(),
            ], [
                'button_counter' => DB::raw('button_counter + 1')
            ]);
        } catch (\Throwable $e) {
            Log::debug('click tracking error', [
                'message' => $e->getMessage()
            ]);
        }

        return response()->json([], 201);
    }

}
