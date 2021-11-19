<?php

namespace App\Http\Controllers;

use App\DomainInformation;
use Carbon\Carbon;
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
        if (isset($request->domains)) {
            if (DomainInformationController::multipleCreation($request->domains, $userId)) {
                flash()->overlay(__('Domains added successfully'), ' ')->success();
            } else {
                flash()->overlay(__('All domains are not valid'), ' ')->error();
                return Redirect::back();
            }
        } else {
            if (DomainInformation::isValidDomain($request->domain)) {
                $monitoring = new DomainInformation($request->all());
                $monitoring->user_id = $userId;
                $monitoring->save();
                flash()->overlay(__('Domain added successfully'), ' ')->success();
            } else {
                flash()->overlay(__('There is no such domain'), ' ')->error();
                return Redirect::back();
            }
        }

        return Redirect::route('domain.information');
    }

    /**
     * @param $domains
     * @param $userId
     * @return bool|null
     */
    public static function multipleCreation($domains, $userId): ?bool
    {
        $newRecord = [];
        $domains = explode("\r\n", $domains);
        foreach ($domains as $domain) {
            $domain = explode(':', $domain);
            if (count($domain) == 3 && DomainInformation::isValidDomain($domain[0])) {
                $newRecord[] = [
                    'user_id' => $userId,
                    'domain' => $domain[0],
                    'check_dns' => (boolean)$domain[1],
                    'check_registration_date' => (boolean)$domain[2],
                ];
            }
        }
        if (count($newRecord) >= 1) {
            DomainInformation::insert($newRecord);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function remove($id): RedirectResponse
    {
        DomainInformation::destroy($id);
        flash()->overlay(__('Domain successfully deleted'), ' ')->success();

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
     * method for cron
     */
    public function checkDomains()
    {
        Log::debug('start check information domains', []);
        $projects = DomainInformation::all();
        foreach ($projects as $project) {
            DomainInformation::checkDomainSock($project);
            $project->save();
        }
        Log::debug('end check information domains', [ ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {
        if ($request->name === 'domain' && strlen($request->option) > 0) {
            if (DomainInformation::isValidDomain($request->option)) {
                DomainInformation::where('id', $request->id)->update([
                    $request->name => $request->option,
                ]);
                return response()->json([]);
            } else {
                return response()->json([], 400);
            }
        }
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

}
