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
        $this->link = route('monitoring.index');
    }

    public function generateTitle(): string
    {
        /** @var User $user */
        $user = Auth::user();
        $projects = $user->monitoringProjects()->get();

        $status = MonitoringProjectUserStatusController::getIdStatusByCode(MonitoringProjectUserStatusController::STATUS_PM);

        $filtered = $projects->pluck('users')->flatten()->unique('id')->filter(function ($val) use ($status) {
            return $val['pivot']['status'] === $status;
        });

        return $filtered->count();
    }

    public function generateDesc(): string
    {
        return __('Project manager count');
    }
}