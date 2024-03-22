<?php


namespace App\Classes\Monitoring\Widgets;


use App\Classes\Monitoring\ProjectsStatisticFacade;

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
        return ProjectsStatisticFacade::getMidTopPct('top30');
    }

    public function generateDesc(): string
    {
        return __('TOP 30%');
    }
}
