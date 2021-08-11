<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Show all users
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = User::all();

        $users->map(function ($user){
            if(!$user->session)
                return true;

            $user->session->agent = $this->createAgent($user->session);
            $user->session->is_current_device = $user->session->id === request()->session()->getId();
            $user->session->last_active = $user->session->last_activity->diffForHumans();

            return $user;
        });

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $role = Role::all()->pluck('name', 'id');
        $lang = collect(Storage::disk('lang')->files())->mapWithKeys(function ($val){
            $str = Str::before($val, '.');
            return [$str => $str];
        });

        return view('users.edit', compact('user', 'role', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param User $user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(User $user, Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'min:3', 'max:255'],
            'role' => ['required'],
        ]);

        $user->update($request->all());

        $user->syncRoles($request->input('role'));

        flash()->overlay(__('User update successfully'), __('Update user'))->success();

        return redirect('users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        if($user->id == Auth::id()){
            flash()->overlay(__('You cannot delete yourself'), __('Error user'))->error();
        }else{
            $user->delete();
            flash()->overlay(__('User deleted successfully'), __('Delete user'))->success();
        }

       return redirect('users');
    }

    /**
     * Create a new agent instance from the given session.
     *
     * @param  mixed  $session
     * @return \Jenssegers\Agent\Agent
     */
    private function createAgent($session)
    {
        return tap(new Agent, function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }
}
