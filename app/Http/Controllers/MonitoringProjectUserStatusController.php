<?php

namespace App\Http\Controllers;

use App\MonitoringProject;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringProjectUserStatusController extends Controller
{
    protected $auth;
    protected $status = [
        'DEF' => 0, // Default
        'TM' => 1, // Team Lead
        'SEO' => 2, // SEO
        'PM' => 3, // Project manager
    ];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->auth = Auth::user();
            return $next($request);
        });
    }

    public function set(Request $request)
    {
        /* @var  User $auth */
        $auth = $this->auth;
        $project = $request->input('project');

        if(!$this->isProjectAdmin($auth, $project))
            abort(403, 'Assign status can only administrator project.');

        $user = User::findOrFail($request->input('user'));
        $this->setStatusUser($user, $project, $request->input('status'));
    }

    public function setStatusUser(User $user, int $project, string $status): bool
    {
        if(!isset($this->status[$status]))
            abort(403, 'wrong user status code.');

        return $user->monitoringProjects()->updateExistingPivot($project, ["status" => $this->status[$status]]);
    }

    public function isProjectAdmin(User $user, int $project)
    {
        return $user->monitoringProjects()->findOrFail($project)->pivot->admin >= 1;
    }
}
