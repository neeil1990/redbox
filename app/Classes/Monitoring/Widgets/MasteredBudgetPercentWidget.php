<?php


namespace App\Classes\Monitoring\Widgets;


use App\User;
use Illuminate\Support\Facades\Auth;

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

        /** @var User $user */
        $user = Auth::user();
        $projects = $user->monitoringProjectsDataTable()->get();

        $projects->transform(function($item){
            $item->master_budget_percent = 0;

            if($item->mastered && $item->budget)
                $item->master_budget_percent = floor($item->mastered / ($item->budget / 30) * 100);

            return $item;
        });

        return $projects->pluck('master_budget_percent')->sum();
    }

    public function generateDesc(): string
    {
        return __('Mastered budget percent');
    }
}
