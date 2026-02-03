<?php

namespace App\Http\Controllers;

use App\Classes\Tariffs\Facades\Tariffs;
use App\Classes\Tariffs\Period\OneDayTariff;
use App\Classes\Tariffs\Period\PeriodTariff;
use App\Classes\Tariffs\Tariff;
use App\Common;
use App\Exports\FilteredUsersExport;
use App\Exports\VerifiedUsersExport;
use App\MainProject;
use App\User;
use App\VisitStatistic;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    protected $tariff;

    public function __construct()
    {

        $this->middleware(['permission:Users']);

        $this->tariff = new Tariffs();
        $this->tariff->setPeriods(new OneDayTariff());
    }

    /**
     * @return void
     */
    public function index(Request $request)
    {
        if ($request->ajax())
            return $this->getDataTable($request);

        $users = User::all();
        $tariffSelect = $this->tariffSelectData();

        return view('users.index', compact('users', 'tariffSelect'));
    }

    protected function getDataTable(Request $request)
    {
        $collection = collect([]);

        $start = $request->get('start');
        $length = $request->get('length');
        $page = ($length) ? ($start / $length) + 1 : false;

        $user = new User();

        $search = $request->get('search');
        if ($search = $search['value'])
            $user = $user->where('email', 'like', $search . '%');

        if ($order = Arr::first($request->get('order'))) {
            $columns = $request->get('columns');
            $user = $user->orderBy($columns[$order['column']]['name'], $order['dir']);
        }

        $users = $user->paginate($length, ['*'], 'page', $page);

        $users->transform(function ($user) {

            // Tariff
            $user->tariff = collect([]);
            if ($pay = $user->pay->where('status', true)->first()) {
                $user->tariff->put('name', $user->tariff()->name());
                $user->tariff->put('active_to', $pay->active_to->format('d.m.Y H:i'));
                $user->tariff->put('active_to_diffForHumans', $pay->active_to->diffForHumans());
            }

            //Created
            $user->created = $user->created_at->format('d.m.Y H:i:s');
            $user->created_diffForHumans = $user->created_at->diffForHumans();

            //Was online
            $loa = $user->last_online_at;
            $user->last_online_strtotime = strtotime($loa);
            $user->last_online = $loa->format('d.m.Y H:i:s');
            $user->last_online_diffForHumans = $loa->diffForHumans();

            return $user;
        });

        $collection->put('draw', $request->input('draw'));
        $collection->put('recordsTotal', $users->total());
        $collection->put('recordsFiltered', $users->total());
        $collection->put('data', collect($users->items()));

        return $collection;
    }

    public function storeTariff(Request $request)
    {
        foreach ($request['users'] as $user) {
            $user = User::find($user);
            $this->assignTariffByUser($user, $request['tariff'], $request['period']);
        }

        return redirect()->back();
    }

    private function assignTariffByUser(User $user, string $tariffCode, string $periodCode): void
    {
        $tariff = $this->tariff->getTariffByCode($tariffCode);
        $tariff->setPeriod($this->tariff->getPeriodByCode($periodCode));

        $user->pay()->update(['status' => false]);

        $user->pay()->create([
            'status' => true,
            'class_tariff' => get_class($tariff),
            'class_period' => get_class($tariff->getPeriod()),
            'sum' => 0,
            'active_to' => Carbon::now()->addDays($tariff->getPeriod()->days())
        ]);

        $tariff->assignRoleByUser($user);
    }

    private function tariffSelectData(): array
    {
        $select = [
            'tariff' => [],
            'period' => [],
        ];

        /* @var Tariff $tariff */
        foreach ($this->tariff->getTariffs() as $tariff)
            $select['tariff'][$tariff->code()] = $tariff->name();

        /* @var PeriodTariff $period */
        foreach ($this->tariff->getPeriods() as $period)
            $select['period'][$period->code()] = $period->name();

        return $select;
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

        $now = Carbon::now()->format('d-m-Y');
        $counterActions = $this->getCounterActions(Carbon::now()->subDays(30)->format('d-m-Y'), $now, $user->id);
        $summedCollection = $this->getActions('20-03-2023 - ' . $now, $user->id);
        $info = VisitStatistic::getModulesInfo($summedCollection);
//        $lastActions = $this->getLastActions(Carbon::now()->subDays(30)->format('d-m-Y'), $now, $user->id);
//        dd($lastActions);

        return view('users.visit', compact('summedCollection', 'info', 'user', 'counterActions'));
    }

    private function getLastActions($start, $end, $userId)
    {
        return VisitStatistic::whereBetween('date', [
            date('Y-m-d', strtotime($start)),
            date('Y-m-d', strtotime($end))
        ])->where('user_id', $userId)
            ->select('project_id', DB::raw('MAX(date) as last_visit'))
            ->groupBy('project_id')
            ->pluck('last_visit', 'project_id');
    }

    public function userActionsHistory(Request $request): JsonResponse
    {
        $collection = $this->getActions($request->dateRange, $request->userId);
        $range = explode(' - ', $request->dateRange);
        $lastActions = $this->getLastActions($range[0], $range[1], $request->userId);

        return response()->json([
            'collection' => $collection,
            'info' => VisitStatistic::getModulesInfo($collection, false),
            'counterActions' => $this->getCounterActions($range[0], $range[1], $request->userId, false),
            'lastActions' => $lastActions
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

    private function getCounterActions($start, $end, $userId, $encode = true)
    {
        $response = VisitStatistic::whereBetween('date', [
            date('Y-m-d', strtotime($start)),
            date('Y-m-d', strtotime($end))
        ])
            ->where('user_id', $userId)
            ->get(['date', 'refresh_page_counter', 'seconds', 'actions_counter'])
            ->groupBy('date')
            ->map(function ($group) {
                $sumActions = $group->sum('actions_counter');
                $sumRefresh = $group->sum('refresh_page_counter');
                $countSeconds = $group->sum('seconds');
                $firstItem = $group->first();
                $firstItem->actionsCounter = $sumActions;
                $firstItem->refreshPageCounter = $sumRefresh;
                $firstItem->time = $countSeconds;
                unset($firstItem->refresh_page_counter);
                unset($firstItem->actions_counter);
                unset($firstItem->seconds);
                unset($firstItem->date);

                return $firstItem;
            });

        $actions = [];
        $refresh = [];
        $time = [];
        $date = [];

        foreach ($response->toArray() as $key => $item) {
            $actions[] = $item['actionsCounter'];
            $refresh[] = $item['refreshPageCounter'];
            $time[] = $item['time'];
            $date[] = $key;
        }

        if ($encode) {
            return json_encode([
                'actions' => $actions,
                'refresh' => $refresh,
                'time' => $time,
                'data' => $date
            ]);
        }

        return [
            'actions' => $actions,
            'refresh' => $refresh,
            'time' => $time,
            'data' => $date
        ];

    }

    public function userVisitStatistics()
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }
        $users = User::where('statistic', 1)->with('roles')->get(['id', 'name', 'last_name', 'email', 'metrics']);
        $usersIds = $users->pluck('id')->toArray();
        $users = $users->groupBy('id')->toArray();
        $statistics = VisitStatistic::whereIn('user_id', $usersIds)->get()->groupBy('user_id');
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
