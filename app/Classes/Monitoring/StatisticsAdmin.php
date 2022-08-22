<?php


namespace App\Classes\Monitoring;


use App\Jobs;
use App\MonitoringProject;
use App\MonitoringStat;

class StatisticsAdmin
{
    protected $projects;
    protected $jobs;
    protected $stat;

    public function __construct()
    {
        $this->projects = new MonitoringProject();
        $this->jobs = new Jobs();
        $this->stat = new MonitoringStat();
    }

    public function getCountOfCheckUpForCurrentDay()
    {
        $name = __('Count of check up for current day');
        $val = $this->stat->currentDay()->count();

        if(!$val)
            return null;

        return collect(['name' => $name, 'val' => $val]);
    }

    public function getCountOfCheckUpForCurrentMonth()
    {
        $name = __('Count of check up for current month');
        $val = $this->stat->currentMonth()->count();

        if(!$val)
            return null;

        return collect(['name' => $name, 'val' => $val]);
    }

    public function getCountOfErrorsForCurrentDay()
    {
        $name = __('Count of errors for current day');
        $val = $this->stat->withErrors()->currentDay()->count();

        if(!$val)
            return null;

        return collect(['name' => $name, 'val' => $val]);
    }

    public function getCountOfProjects()
    {
        $name = __('Count of projects');
        $val = $this->projects->count();

        if(!$val)
            return null;

        return collect(['name' => $name, 'val' => $val]);
    }

    public function getCountOfTasksInQueue()
    {
        $name = __('Count of tasks in queue');
        $val = $this->jobs->positionsQueue()->count();

        if(!$val)
            return null;

        return collect(['name' => $name, 'val' => $val]);
    }

}
