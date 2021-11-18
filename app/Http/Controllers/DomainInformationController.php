<?php

namespace App\Http\Controllers;

use App\DomainInformation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class DomainInformationController extends Controller
{
    public $counter;

    public function index()
    {
        $projects = DomainInformation::where('user_id', '=', Auth::id())->get();
        $countProjects = count($projects);

        if ($countProjects === 0) {
            return $this->createView();
        }

        return view('domain-information.index', compact('projects', 'countProjects'));
    }

    public function createView()
    {
        return view('domain-information.create');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        $monitoring = new DomainInformation($request->all());
        $monitoring->domain = DomainInformationController::removeProtocol($request);
        $monitoring->user_id = $userId;
        $monitoring->save();

        flash()->overlay(__('Monitoring was successfully created'), ' ')->success();
        return Redirect::route('domain.information');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function remove($id): RedirectResponse
    {
        DomainInformation::destroy($id);
        flash()->overlay(__('Monitoring was successfully deleted'), ' ')->success();

        return Redirect::route('domain.information');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function checkDomain($id): RedirectResponse
    {
        $project = DomainInformation::findOrFail($id);
        DomainInformation::checkDomainSock($project);
        $project->save();
        return Redirect::back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {
        if (strlen($request->option) > 0) {
            DomainInformation::where('id', $request->id)->update([
                $request->name => $request->option,
            ]);
            return response()->json([]);
        }
        return response()->json([], 400);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeDomains(Request $request): JsonResponse
    {
        if (DomainInformation::destroy(explode(',', $request->ids))) {
            return response()->json([]);
        }
        return response()->json([], 400);
    }

    /**
     * @param $request
     * @return string|string[]|null
     */
    public static function removeProtocol($request)
    {
        $link = preg_replace('#^https?://#', '', rtrim($request->domain, '/'));
        return preg_replace('/^www\./', '', $link);
    }
}
