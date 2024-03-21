<?php


namespace App\Classes\Monitoring\Widgets;


use App\Http\Controllers\MonitoringProjectUserStatusController;
use App\User;
use Illuminate\Support\Facades\Auth;

class SeoUserCountWidget extends WidgetsAbstract
{
    public function __construct()
    {
        $this->code = 'SEO_USER_COUNT';
        $this->name = __('Seo user count');
        $this->icon = 'fas fa-laptop-code';
    }

    public function generateTitle(): string
    {
        /** @var User $user */
        $user = Auth::user();
        $projects = $user->monitoringProjects()->get();

        $status = MonitoringProjectUserStatusController::getIdStatusByCode(MonitoringProjectUserStatusController::STATUS_SEO);

        $filtered = $projects->pluck('users')->flatten()->filter(function ($val) use ($status) {
            return $val['pivot']['status'] === $status;
        })->unique('id');

        return $filtered->count();
    }

    public function generateDesc(): string
    {
        return __('Seo user count');
    }
}
