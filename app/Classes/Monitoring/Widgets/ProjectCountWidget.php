<?php


namespace App\Classes\Monitoring\Widgets;

use App\User;
use Illuminate\Support\Facades\Auth;

class ProjectCountWidget extends WidgetsAbstract
{
    public function __construct()
    {
        $this->code = 'PROJECT_COUNT';
        $this->name = __('Projects count');
        $this->link = route('monitoring.index');
        $this->icon = 'fas fa-tasks';
    }

    public function generateTitle(): string
    {
        /** @var User $user */
        $user = Auth::user();
        return $user->monitoringProjects()->count();
    }

    public function generateDesc(): string
    {
        return __('Projects count');
    }

}
