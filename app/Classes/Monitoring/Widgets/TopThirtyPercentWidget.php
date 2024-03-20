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
        if(!$statistics = $this->user->statistics()->monitoringProjectsNow()->first())
            return '0';

        return $statistics['monitoring_project']->pluck('top30')->sum();
    }

    public function generateDesc(): string
    {
        return __('TOP 30%');
    }
}
