<?php


namespace App\Classes\Monitoring\Widgets;


use App\User;
use Illuminate\Support\Facades\Auth;

class MasteredBudgetWidget extends WidgetsAbstract
{
    public function __construct()
    {
        $this->code = 'MASTERED_BUDGET';
        $this->name = __('Mastered budget');
        $this->icon = 'fas fa-wallet';
    }

    public function generateTitle(): string
    {
        if(!$statistics = $this->user->statistics()->monitoringProjectsNow()->first())
            return '0';

        return $statistics['monitoring_project']->pluck('mastered')->sum();
    }

    public function generateDesc(): string
    {
        return __('Mastered budget');
    }
}
