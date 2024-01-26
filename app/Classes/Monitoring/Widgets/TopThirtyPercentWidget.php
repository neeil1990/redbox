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
        $this->link = route('monitoring.index');
    }

    public function generateTitle(): string
    {

        /** @var User $user */
        $user = Auth::user();
        $projects = $user->monitoringProjectsWithDataTable()->get();

        return $projects->pluck('top30')->sum();
    }

    public function generateDesc(): string
    {
        return __('TOP 30%');
    }
}
