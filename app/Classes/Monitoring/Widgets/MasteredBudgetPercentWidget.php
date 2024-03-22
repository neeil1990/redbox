<?php


namespace App\Classes\Monitoring\Widgets;


use App\Classes\Monitoring\ProjectsStatisticFacade;

class MasteredBudgetPercentWidget extends WidgetsAbstract
{
    public function __construct()
    {
        $this->code = 'MASTERED_BUDGET_PERCENT';
        $this->name = __('Mastered budget percent');
        $this->icon = 'fas fa-chart-pie';
    }

    public function generateTitle(): string
    {
        return ProjectsStatisticFacade::getMidMasteredBudgetPct();
    }

    public function generateDesc(): string
    {
        return __('Mastered budget percent');
    }
}
