<?php


namespace App\Classes\Monitoring\Widgets;


use App\User;
use Illuminate\Support\Facades\Auth;

class MasteredBudgetWidget extends WidgetsAbstract
{
    public function __construct()
    {
        $this->code = 'MASTERED_BUDGET';
        $this->name = __('Mastered budget');
        $this->icon = 'fas fa-wallet';
    }

    public function generateTitle(): string
    {
        $statistics = $this->user->statistics()->monitoringProjectsNow()->first();
        $projects = $statistics['monitoring_project'];

        return $projects->pluck('mastered')->sum();
    }

    public function generateDesc(): string
    {
        return __('Mastered budget');
    }
}
