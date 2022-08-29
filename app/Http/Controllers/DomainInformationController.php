<?php

namespace App\Http\Controllers;

use App\DomainInformation;
use App\TariffSetting;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\VarDumper\VarDumper;

class DomainInformationController extends Controller
{
    public $counter;

    public function __construct()
    {
        $this->middleware(['permission:Domain information']);
    }

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
        $user = User::find(Auth::id());

        if (isset($request->domains)) {
            $count = count(explode("\r\n", $request->domains));
        } else {
            $count = 0;
        }

        if (TariffSetting::checkDomainInformationLimits($user, $count)) {
            flash()->overlay(__('Your limits are exhausted'), ' ')->error();

            return redirect()->route('domain.information');
        }

        if (isset($request->domains)) {
            DomainInformationController::multipleCreation($request->domains, $user);
        } else {

            $domain = DomainInformation::getDomain($request->domain);

            if (DomainInformation::isValidDomain($domain)) {
                $monitoring = new DomainInformation($request->all());
                $monitoring->domain = $domain;
                $monitoring->user_id = $user->id;
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
     * @param $user
     * @return void
     */
    public static function multipleCreation($domains, $user)
    {
        $newRecord = [];
        $domains = explode("\r\n", $domains);
        $domains = array_diff($domains, array(''));
        foreach ($domains as $item) {

            $domain = DomainInformation::getDomain($item);
            $obj = explode(':', $item);
            $counter = count($obj);
            $checkRegistrationDate = explode('/', $obj[$counter - 1]);

            if (count($obj) == 4 || count($obj) == 3 && DomainInformation::isValidDomain($domain)) {
                $newRecord[] = [
                    'user_id' => $user->id,
                    'domain' => $domain,
                    'check_dns' => (boolean)$obj[$counter - 2],
                    'check_registration_date' => (boolean)$checkRegistrationDate[0],
                ];
            }
        }

        if (count($newRecord) >= 1) {
            DomainInformation::insert($newRecord);
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
        DomainInformation::checkDomain($project);

        return Redirect::back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {
        if ($request->name === 'domain' && strlen($request->option) > 0) {
            $domain = DomainInformation::getDomain($request->option);
            if (DomainInformation::isValidDomain($domain)) {
                DomainInformation::where('id', $request->id)->update([
                    $request->name => $domain,
                ]);
                return response()->json([
                    'message' => $domain
                ]);
            }
        } elseif (strlen($request->option) > 0) {
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
