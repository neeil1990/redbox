<?php


namespace App\Classes\Monitoring\Widgets;


use App\User;
use Illuminate\Support\Facades\Auth;

class TopOneHundredPercentWidget extends WidgetsAbstract
{
    public function __construct()
    {
        $this->code = 'TOP_ONE_HUNDRED_PERCENT';
        $this->name = __('Top 100%');
        $this->icon = 'fas fa-percent';
    }

    public function generateTitle(): string
    {
        $statistics = $this->user->statistics()->monitoringProjectsNow()->first();
        $projects = $statistics['monitoring_project'];

        return $projects->pluck('top100')->sum();
    }

    public function generateDesc(): string
    {
        return __('TOP 100%');
    }
}
