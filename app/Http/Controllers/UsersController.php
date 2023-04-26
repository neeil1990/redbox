<?php

namespace App\Http\Controllers;

use App\Common;
use App\Exports\FilteredUsersExport;
use App\Exports\VerifiedUsersExport;
use App\MainProject;
use App\User;
use App\VisitStatistic;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Users']);
    }

    /**
     * @return void
     */
    public function index()
    {
        $users = User::all();
        foreach ($users as $key => $user) {
            $metrics = json_decode($user['metrics'], true);
            if ($metrics !== null) {
                $users[$key]['metrics'] = $metrics;
            }
        }

        return view('users.index', compact('users'));
    }

    /**
     * @param $id
     * @return Authenticatable
     */
    public function login($id)
    {
        if (Auth::loginUsingId($id))
            return redirect('/');
        else
            return redirect('users');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit(User $user)
    {
        $role = Role::all()->pluck('name', 'id')->map(function ($val) {
            return __($val);
        });

        $lang = collect(Storage::disk('lang')->files())->mapWithKeys(function ($val) {
            $str = Str::before($val, '.');
            return [$str => __($str)];
        });

        $superAdmin = in_array(3, Auth::user()->role->toArray());

        if (!$superAdmin) {
            unset($role[3]);
        }

        return view('users.edit', compact('user', 'role', 'lang', 'superAdmin'));
    }

    /**
     * @param User $user
     * @param Request $request
     * @return Application|RedirectResponse|Redirector|void
     * @throws ValidationException
     */
    public function update(User $user, Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'min:3', 'max:255'],
            'role' => ['required'],
            'password' => ['nullable', 'min:8']
        ]);

        $user->update($request->all());
        $user->syncRoles($request->input('role'));

        if ($request->input('password') !== null && in_array(3, Auth::user()->role->toArray())) {
            $user->password = Hash::make($request->input('password'));
            $user->setRememberToken(Str::random(60));

            $user->save();
        }

        if ($user->lang == 'en') {
            flash()->overlay('User update successfully', 'Notification')->success();
        } else {
            flash()->overlay('Даные пользователя успешно обновлены', 'Уведомление')->success();
        }

        return redirect('users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return Response
     * @throws Exception
     */
    public function destroy(User $user)
    {
        if ($user->id == Auth::id()) {
            flash()->overlay(__('You cannot delete yourself'), __('Error user'))->error();
        } else {
            $user->delete();
            flash()->overlay(__('User deleted successfully'), __('Delete user'))->success();
        }

        return redirect('users');
    }

    /**
     * Create a new agent instance from the given session.
     *
     * @param mixed $session
     * @return Agent
     */
    private function createAgent($session)
    {
        return tap(new Agent, function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }

    public function getFile($type)
    {
        if (User::isUserAdmin()) {
            $file = Excel::download(new VerifiedUsersExport(), 'verified_users.' . $type);
            Common::fileExport($file, $type, 'verified-users');
        } else {
            abort(403);
        }

    }

    public function filterExportsUsers(Request $request)
    {
        if (User::isUserAdmin()) {
            $file = Excel::download(new FilteredUsersExport($request->all()), 'filtered_users.' . $request->fileType);
            Common::fileExport($file, $request->fileType, 'verified-users');
        } else {
            abort(403);
        }
    }

    public function visitStatistics(User $user)
    {
        if (Auth::id() !== $user->id && !User::isUserAdmin()) {
            return abort(403);
        }

        $summedCollection = $this->getActions('20-03-2023 - ' . Carbon::now()->format('d-m-Y'), $user->id);
        $info = VisitStatistic::getModulesInfo($summedCollection);

        return view('users.visit', compact('summedCollection', 'info', 'user'));
    }

    public function userActionsHistory(Request $request): JsonResponse
    {
        $collection = $this->getActions($request->dateRange, $request->userId);

        return response()->json([
            'collection' => $collection,
            'info' => VisitStatistic::getModulesInfo($collection, false),
        ]);
    }

    public function getDateRangeVisitStatistics(User $user): JsonResponse
    {
        if (Auth::id() !== $user->id && !User::isUserAdmin()) {
            return abort(403);
        }

        return response()->json([
            'dates' => VisitStatistic::where('user_id', $user->id)
                ->groupBy('date')
                ->get('date')
                ->toArray()
        ]);
    }

    private function getActions($dateRange, $userId)
    {
        $range = explode(' - ', $dateRange);

        return VisitStatistic::whereBetween('date', [
            date('Y-m-d', strtotime($range[0])),
            date('Y-m-d', strtotime($range[1]))
        ])
            ->where('user_id', $userId)
            ->with('project')
            ->get()
            ->groupBy('project_id')
            ->map(function ($group) {
                $sumActions = $group->sum('actions_counter');
                $sumRefresh = $group->sum('refresh_page_counter');
                $countSeconds = $group->sum('seconds');
                $firstItem = $group->first();
                $firstItem->actionsCounter = $sumActions;
                $firstItem->refreshPageCounter = $sumRefresh;
                $firstItem->time = Common::secondsToDate($countSeconds);

                return $firstItem;
            });
    }

    public function userVisitStatistics()
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }
        $users = User::with('roles')->get(['id', 'name', 'last_name', 'email', 'metrics'])->groupBy('id')->toArray();
        $statistics = VisitStatistic::get()->groupBy('user_id');
        $results = [];

        foreach ($statistics as $userId => $statistic) {
            foreach ($statistic as $info) {
                if (isset($results[$userId]['stat']['actions_counter'])) {
                    $results[$userId]['stat']['actions_counter'] += $info['actions_counter'];
                } else {
                    $results[$userId]['stat']['actions_counter'] = $info['actions_counter'];
                }

                if (isset($results[$userId]['stat']['refresh_page_counter'])) {
                    $results[$userId]['stat']['refresh_page_counter'] += $info['refresh_page_counter'];
                } else {
                    $results[$userId]['stat']['refresh_page_counter'] = $info['refresh_page_counter'];
                }

                if (isset($results[$userId]['stat']['seconds'])) {
                    $results[$userId]['stat']['seconds'] += $info['seconds'];
                } else {
                    $results[$userId]['stat']['seconds'] = $info['seconds'];
                }

                if (empty($results[$userId]['userInfo'])) {
                    $results[$userId]['userInfo'] = $info['user']->toArray();
                }

                if (empty($results[$userId]['userInfo']['tariff'])) {
                    $results[$userId]['userInfo']['tariff'] = self::getRoles($users[$userId][0]['roles']);
                }
            }
        }

        return view('users.statistics', compact('results'));
    }

    public static function getRoles(array $array): array
    {
        $roles = [];

        foreach ($array as $item) {
            $roles[] = $item['name'];
        }

        return $roles;
    }
}
