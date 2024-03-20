<?php


namespace App\Classes\Monitoring\Widgets;


use App\User;
use Illuminate\Support\Facades\Auth;

class MaxBudgetWidget extends WidgetsAbstract
{
    public function __construct()
    {
        $this->code = 'MAX_BUDGET';
        $this->name = __('Max budget');
        $this->icon = 'fas fa-ruble-sign';
    }

    public function generateTitle(): string
    {
        $statistics = $this->user->statistics()->monitoringProjectsNow()->first();
        $projects = $statistics['monitoring_project'];

        return $projects->pluck('budget')->sum();
    }

    public function generateDesc(): string
    {
        return __('Max budget');
    }
}
