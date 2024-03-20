<?php


namespace App\Classes\Monitoring\Widgets;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TopTenPercentWidget extends WidgetsAbstract
{
    public function __construct()
    {
        $this->code = 'TOP_TEN_PERCENT';
        $this->name = __('Top 10%');
        $this->icon = 'fas fa-percent';
    }

    public function generateTitle(): string
    {
        $statistics = $this->user->statistics()->monitoringProjectsNow()->first();
        $projects = $statistics['monitoring_project'];

        return $projects->pluck('top10')->sum();
    }

    public function generateDesc(): string
    {
        return __('TOP 10%');
    }
}
