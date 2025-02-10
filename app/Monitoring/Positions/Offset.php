<?php


namespace App\Monitoring\Positions;


use App\MonitoringProject;

class Offset
{
    protected $project;
    protected $engineID;
    protected $startDate;
    protected $endDate;
    protected $positionFrom;
    protected $positionTo;
    protected $position;
    protected $operator;

    public function __construct(int $projectID, int $engineID)
    {
        $this->project = MonitoringProject::findOrFail($projectID);
        $this->engineID = $engineID;
    }

    public function execute(string $startDate, string $endDate)
    {

    }

}
