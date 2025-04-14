<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MonitoringPermissionsController extends Controller
{
    public function index()
    {
        $roles = Role::where('name', 'like', '%_monitoring')->get();
        $permissions = Permission::where('name', 'like', '%\_monitoring')->get();

        return view('monitoring.permissions', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $permissions = $request->input('permissions');

        foreach ($permissions as $role => $permission) {
            $role = Role::where('name', $role)->first();
            $role->syncPermissions(array_keys($permission));
        }

        return response()->json([
            'status' => true,
            'message' => 'Права сохранены!'
        ]);
    }

    public function getRoleOptions()
    {
        $roles = Role::where('name', 'like', '%_monitoring')->get();

        $roles->transform(function ($item) {
            $item['val'] = $item['name'];
            $item['text'] = $item['title'];

            return $item;
        });

        return $roles;
    }

    public function syncProjectRoles(Request $request)
    {
        $id = $request->input('project');
        $role = $request->input('status');

        $user = User::findOrFail($request->input('user'));

        apply_team_permissions($id);

        $user->syncRoles([$role]);
    }
}
