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
        $this->link = route('monitoring.index');
    }

    public function generateTitle(): string
    {

        /** @var User $user */
        $user = Auth::user();
        $projects = $user->monitoringProjectsWithDataTable()->get();

        return $projects->pluck('mastered')->sum();
    }

    public function generateDesc(): string
    {
        return __('Mastered budget');
    }
}
