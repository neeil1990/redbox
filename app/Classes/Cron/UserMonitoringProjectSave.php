<?php


namespace App\Classes\Cron;

use App\User;

class UserMonitoringProjectSave
{
    const DAY = 28;
    const TIME = '13:00';

    public function __invoke()
    {
        $users = User::all();

        foreach($users as $user){

            $projects = $user->monitoringProjectsWithDataTable()->get();

            if($projects->isEmpty())
                continue;

            foreach($projects as $project)
                $user->statistics()->create(['monitoring_project' => $project]);
        }
    }
}
