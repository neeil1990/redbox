<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ManageAccessController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:Super Admin|admin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view('manage-access.index', compact('roles', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name = $request->input('name');

        $permission = $this->permissions($request->input('type'));

        return $permission->create(['name' => $name]);
    }

    /**
     * @param Request $request
     */
    public function assignPermission(Request $request)
    {
        $role = Role::where('name', $request->input('role'))->first();

        if($request->input('action') === 'assign')
            $role->givePermissionTo($request->input('permission'));

        if($request->input('action') === 'revoke')
            $role->revokePermissionTo($request->input('permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $name = $request->input('name');

        $permission = $this->permissions($request->input('type'));

        $permission->where('id', $id)->update(['name' => $name]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  int  $type
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $type)
    {
        $permission = $this->permissions($type);

        $permission->find($id)->delete();
    }

    /**
     * @param $name
     * @return Permission|Role|null
     */
    private function permissions($name)
    {
        $class = null;

        if($name === 'role')
            $class = new Role();

        if($name === 'permission')
            $class = new Permission();

        return $class;
    }
}
