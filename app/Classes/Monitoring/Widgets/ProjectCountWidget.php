<?php


namespace App\Classes\Monitoring\Widgets;

use App\User;
use Illuminate\Support\Facades\Auth;

class ProjectCountWidget extends WidgetsAbstract
{
    public function __construct()
    {
        $this->code = 'PROJECT_COUNT';
        $this->name = __('Project count');
        $this->link = route('monitoring.index');
    }

    public function generateTitle(): string
    {
        /** @var User $user */
        $user = Auth::user();
        return $user->monitoringProjects()->count();
    }

    public function generateDesc(): string
    {
        return __('Project count');
    }

}
