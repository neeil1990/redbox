<?php


namespace App\Monitoring\Services;


use App\MonitoringProject;
use App\User;

class MonitoringUserService
{
    public function getMonitoringAdminUser(MonitoringProject $project): ?User
    {
        apply_team_permissions($project['id']);

        $user = null;

        foreach ($project->users as $model) {
            $model->unsetRelation('roles');

            if ($model->HasRole('admin_monitoring')) {
                $user = $model;
            }
        }

        apply_global_team_permissions();

        return $user;
    }
}
