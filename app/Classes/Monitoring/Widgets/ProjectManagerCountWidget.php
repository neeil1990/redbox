<?php


namespace App\Classes\Monitoring\Widgets;


use App\Http\Controllers\MonitoringProjectUserStatusController;
use App\User;
use Illuminate\Support\Facades\Auth;

class ProjectManagerCountWidget extends WidgetsAbstract
{
    public function __construct()
    {
        $this->code = 'PROJECT_MANAGER_COUNT';
        $this->name = __('Project manager count');
        $this->icon = 'fas fa-user';
    }

    public function generateTitle(): string
    {
        /** @var User $user */
        $user = Auth::user();
        $projects = $user->monitoringProjects()->get();

        $status = MonitoringProjectUserStatusController::getIdStatusByCode(MonitoringProjectUserStatusController::STATUS_PM);

        $filtered = $projects->pluck('users')->flatten()->filter(function ($val) use ($status) {
            return $val['pivot']['status'] === $status;
        })->unique('id');

        return $filtered->count();
    }

    public function generateDesc(): string
    {
        return __('Project manager count');
    }
}
