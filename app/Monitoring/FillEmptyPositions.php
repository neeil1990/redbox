<?php


namespace App\Monitoring;


use App\Events\MonitoringPositionInsert;
use App\MonitoringKeyword;
use App\MonitoringProject;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class FillEmptyPositions
{
    protected $engine_id;
    protected $startDate;
    protected $endDate;
    protected $period;
    protected $project;
    protected $min = 1;
    protected $max = 20;

    public function __construct($project_id, $engine_id, $startDate, $endDate)
    {
        $this->project = MonitoringProject::findOrFail($project_id);
        $this->engine_id = $engine_id;
        $this->startDate = Carbon::parse($startDate);
        $this->endDate = Carbon::parse($endDate);
        $this->period = CarbonPeriod::create($startDate, '1 day', $endDate);
    }

    public function execute()
    {
        foreach ($this->project->keywords as $query) {

            $min = $this->getMinPosition($query);

            if ($min > $this->max) {
                $this->max = $min;
                $this->min = $min - 10;
            } else {
                $this->min = $min;
            }

            foreach ($this->period as $period) {
                $date = $period->format('Y-m-d');

                if ($this->positionDoesntExist($query, $date)) {
                    $this->addPosition($query, $date, rand($this->min, $this->max));
                }
            }
        }
    }

    protected function getMinPosition(MonitoringKeyword $keyword)
    {
        $min = $keyword->positions()->where('monitoring_searchengine_id', $this->engine_id)
            ->whereDate('created_at', '<', $this->startDate)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get('position')->min('position');

        return $min;
    }

    protected function positionDoesntExist(MonitoringKeyword $keyword, $date)
    {
        return $keyword->positions()
            ->where('monitoring_searchengine_id', $this->engine_id)
            ->whereDate('created_at', $date)
            ->doesntExist();
    }

    protected function addPosition(MonitoringKeyword $keyword, $date, $position)
    {
        $positions = $keyword->positions()->make([
            'monitoring_searchengine_id' => $this->engine_id,
            'position' => $position,
            'created_at' => $date,
            'updated_at' => $date,
        ])->load('keyword');

        broadcast(new MonitoringPositionInsert($positions));
    }

}
