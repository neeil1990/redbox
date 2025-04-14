<?php

namespace App\Http\Controllers;

use App\MonitoringProject;
use App\MonitoringUserStatus;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MonitoringProjectUserStatusController extends Controller
{
    const STATUS_EMPTY = 'EMPTY'; // Empty
    const STATUS_OWNER = 'OWNER'; // Project owner
    const STATUS_TL = 'TL'; // Team Lead
    const STATUS_PM = 'PM'; // Project manager
    const STATUS_SEO = 'SEO'; // SEO

    protected $auth;

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
        return $user->monitoringProjects()->withTimestamps()->updateExistingPivot($project, ["status" => self::getIdStatusByCode($status)]);
    }

    public function isProjectAdmin(User $user, int $project)
    {
        return $user->monitoringProjects()->findOrFail($project)->pivot->admin >= 1;
    }

    static public function getOptions()
    {
        $options = MonitoringUserStatus::all();

        $options->transform(function($item){
            $item['val'] = $item['code'];
            $item['text'] = $item['name'];

            return $item;
        });

        return $options;
    }

    static public function getStatusByCode(string $code): MonitoringUserStatus
    {
        return MonitoringUserStatus::where('code', $code)->firstOrFail();
    }

    static public function getStatusById(int $id): MonitoringUserStatus
    {
        return MonitoringUserStatus::findOrFail($id);
    }

    static public function getIdStatusByCode(string $code): int
    {
        $status = self::getStatusByCode($code);
        return $status['id'];
    }
}
