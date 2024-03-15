<?php


namespace App\Classes\Cron;

use App\Classes\Monitoring\ProjectData;
use App\User;
use Carbon\Carbon;

class UserMonitoringProjectSave
{
    const TIME = '13:00';

    public function __invoke()
    {
        $users = User::all();

        foreach($users as $user){

            $projects = $user->monitoringProjectsDataTable()->get();

            if($projects->isEmpty())
                continue;

            foreach($projects as $project)
            {
                $projectData = new ProjectData($project);
                $projectData->extension();

                $user->statistics()->create(['monitoring_project' => $project]);
            }
        }
    }

    static public function storeDate()
    {
        $carbon = Carbon::now()->lastOfMonth();
        return $carbon;
    }
}
