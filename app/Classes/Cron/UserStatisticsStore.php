<?php


namespace App\Classes\Cron;

use App\Classes\Monitoring\ProjectDataFacade;
use App\User;
use App\UsersStatistic;
use Carbon\Carbon;

class UserStatisticsStore
{
    public function __invoke()
    {
        $this->monitoringProjectsStore();
    }

    protected function monitoringProjectsStore()
    {
        $data = [];
        $users = User::all();

        foreach($users as $user){

            $projects = $user->monitoringProjectsDataTable()->get();

            if($projects->isEmpty())
                continue;

            ProjectDataFacade::projectsExtension($projects);

            $data[] = [
                'user_id' => $user['id'],
                'monitoring_project' => $projects,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        $this->store($data);
    }

    protected function store($data = []): void
    {
        UsersStatistic::insert($data);
    }
}
