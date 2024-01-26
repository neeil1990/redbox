<?php


namespace App\Classes\Monitoring\Widgets;


use App\User;
use Illuminate\Support\Facades\Auth;

class MaxBudgetWidget extends WidgetsAbstract
{
    public function __construct()
    {
        $this->code = 'MAX_BUDGET';
        $this->name = __('Max budget');
        $this->link = route('monitoring.index');
    }

    public function generateTitle(): string
    {

        /** @var User $user */
        $user = Auth::user();
        $projects = $user->monitoringProjectsWithDataTable()->get();

        return $projects->pluck('budget')->sum();
    }

    public function generateDesc(): string
    {
        return __('Max budget');
    }
}
