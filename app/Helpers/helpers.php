<?php
use Illuminate\Support\Facades\Auth;

if (! function_exists('apply_team_permissions')) {
    function apply_team_permissions(int $id): void
    {
        $user = Auth::user();

        if ($user) {
            setPermissionsTeamId($id);
            $user->unsetRelation('roles', 'permissions');
        }
    }
}

if (! function_exists('apply_global_team_permissions')) {
    function apply_global_team_permissions(): void
    {
        $global_team = 1;
        apply_team_permissions($global_team);
    }
}

if (! function_exists('get_team_permission_id')) {
    function get_team_permission_id()
    {
        return app(\Spatie\Permission\PermissionRegistrar::class)->getPermissionsTeamId();
    }
}
