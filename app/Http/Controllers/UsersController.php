<?php

namespace App\Http\Controllers;

use App\Common;
use App\Exports\VerifiedUsersExport;
use App\User;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
//
//        $users->map(function ($user) {
//            if (!$user->session)
//                return true;
//
////            $user->session->agent = $this->createAgent($user->session);
////            $user->session->is_current_device = $user->session->id === request()->session()->getId();
////            $user->session->last_active = $user->session->last_activity->diffForHumans();
//
//            return $user;
//        });

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
            flash()->overlay('User update successfully', ' ')->success();
        } else {
            flash()->overlay('Даные пользователя успешно обновлены', ' ')->success();
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

    /**
     * @param $type
     * @return void
     */
    public function getFile($type)
    {
        if (User::isUserAdmin()) {
            $file = Excel::download(new VerifiedUsersExport(), 'verified_users.' . $type);
            Common::fileExport($file, $type, 'verified-users');
        } else {
            abort(403);
        }

    }
}
