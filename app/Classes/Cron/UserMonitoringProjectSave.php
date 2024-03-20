<?php


namespace App\Classes\Cron;

use App\Classes\Monitoring\ProjectDataFacade;
use App\User;
use Carbon\Carbon;

class UserMonitoringProjectSave
{
    const TIME = '00:00';

    public function __invoke()
    {
        $users = User::all();

        foreach($users as $user){

            $projects = $user->monitoringProjectsDataTable()->get();

            if($projects->isEmpty())
                continue;

            ProjectDataFacade::projectsExtension($projects);

            $user->statistics()->create(['monitoring_project' => $projects]);
        }
    }

    static public function storeDate()
    {
        $carbon = Carbon::now()->lastOfMonth();
        return $carbon;
    }
}
