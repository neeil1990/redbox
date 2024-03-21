<?php


namespace App\Classes\Monitoring\Widgets;


use App\Classes\Monitoring\ProjectsStatisticFacade;
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
        $projects = ProjectsStatisticFacade::getTodayProjects();

        return $projects->pluck('mastered')->sum();
    }

    public function generateDesc(): string
    {
        return __('Mastered budget');
    }
}
