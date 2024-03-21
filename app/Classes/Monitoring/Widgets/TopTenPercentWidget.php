<?php


namespace App\Classes\Monitoring\Widgets;

use App\Classes\Monitoring\ProjectsStatisticFacade;

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
        return ProjectsStatisticFacade::getMidTopPct('top10');
    }

    public function generateDesc(): string
    {
        return __('TOP 10%');
    }
}
