<?php


namespace App\Classes\Monitoring\Widgets;


use App\Classes\Monitoring\ProjectsStatisticFacade;

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
        return ProjectsStatisticFacade::getMidTopPct('top100');
    }

    public function generateDesc(): string
    {
        return __('TOP 100%');
    }
}
