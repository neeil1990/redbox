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
        if(!$statistics = $this->user->statistics()->monitoringProjectsNow()->first())
            return '0';

        return $statistics['monitoring_project']->pluck('budget')->sum();
    }

    public function generateDesc(): string
    {
        return __('Max budget');
    }
}
