<?php

namespace App\Classes\Monitoring;

use App\MonitoringDataTableColumnsProject;
use App\MonitoringKeyword;
use App\MonitoringPosition;
use App\MonitoringProject;

class ProjectData
{
    protected $project;
    protected $budget = null;
    protected $queries;
    protected $positions;
    protected $result = [];

    public function __construct(MonitoringProject $project)
    {
        $this->project = $project;
        $calculate = new ProjectDependencies($project);

        $this->queries = $calculate->getQueries();
        $this->positions = $calculate->getLatestPositionCollect();

        $this->resultInit();
    }

    private function resultInit()
    {
        $this->percentCalc();
        $this->masteredCalc();

        $this->result['words'] = $this->queries->count();
    }

    public function save()
    {
        $this->store($this->result);
    }

    public function extension()
    {
        $this->project->fill($this->result);
    }

    private function masteredCalc()
    {
        $mastered = new Mastered($this->positions);

        $this->result['mastered'] = $mastered->total();
        $this->result['mastered_percent'] = $mastered->percentOf($this->budget);
        $this->result['mastered_info'] = collect([
            'top1' => $mastered->top1(),
            'top3' => $mastered->top3(),
            'top5' => $mastered->top5(),
            'top10' => $mastered->top10(),
            'top20' => $mastered->top20(),
            'top50' => $mastered->top50(),
            'top100' => $mastered->top100(),
            'total' => $mastered->total(),
        ]);
    }

    private function percentCalc()
    {
        $percentOfTop = new PercentCalculate($this->positions->pluck('position'));

        $this->result['top3'] = $percentOfTop->top3();
        $this->result['top5'] = $percentOfTop->top5();
        $this->result['top10'] = $percentOfTop->top10();
        $this->result['top30'] = $percentOfTop->top30();
        $this->result['top100'] = $percentOfTop->top100();
        $this->result['middle'] = $percentOfTop->middle();
    }

    public function store(array $data)
    {
        MonitoringDataTableColumnsProject::updateOrCreate(
            ['monitoring_project_id' => $this->project['id']],
            $data
        );
    }
}
