<?php


namespace App\Classes\Cron;

use App\Classes\Monitoring\ProjectDataFacade;
use App\User;

class UserStatisticsStore
{
    public function __invoke()
    {
        $this->monitoringProjectsStore();
    }

    protected function monitoringProjectsStore()
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
}
