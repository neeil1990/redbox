<?php

namespace App\Http\Controllers;

use App\MonitoringProject;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringProjectUserStatusController extends Controller
{
    const STATUS_DEF = 0;
    const STATUS_TL = 1;
    const STATUS_SEO = 2;
    const STATUS_PM = 3;
    const STATUS_OWNER = 4;

    protected $auth;
    protected $status = [
        'DEF' => self::STATUS_DEF, // Default
        'TL' => self::STATUS_TL, // Team Lead
        'SEO' => self::STATUS_SEO, // SEO
        'PM' => self::STATUS_PM, // Project manager
        'OWNER' => self::STATUS_OWNER, // Project owner
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

        return $user->monitoringProjects()->withTimestamps()->updateExistingPivot($project, ["status" => $this->status[$status]]);
    }

    public function isProjectAdmin(User $user, int $project)
    {
        return $user->monitoringProjects()->findOrFail($project)->pivot->admin >= 1;
    }
}
