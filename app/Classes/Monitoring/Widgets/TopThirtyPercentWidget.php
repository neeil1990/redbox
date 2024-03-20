<?php


namespace App\Classes\Monitoring\Widgets;


use App\User;
use Illuminate\Support\Facades\Auth;

class TopThirtyPercentWidget extends WidgetsAbstract
{

    public function __construct()
    {
        $this->code = 'TOP_THIRTY_PERCENT';
        $this->name = __('Top 30%');
        $this->icon = 'fas fa-percent';
    }

    public function generateTitle(): string
    {
        $statistics = $this->user->statistics()->monitoringProjectsNow()->first();
        $projects = $statistics['monitoring_project'];

        return $projects->pluck('top30')->sum();
    }

    public function generateDesc(): string
    {
        return __('TOP 30%');
    }
}
