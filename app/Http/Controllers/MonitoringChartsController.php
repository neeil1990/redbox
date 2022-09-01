<?php

namespace App\Http\Controllers;

use App\Classes\Monitoring\AreaChartData;
use App\MonitoringPosition;
use App\MonitoringProject;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class MonitoringChartsController extends Controller
{
    protected $project;
    protected $keywords;
    protected $positions;
    protected $region;

    private function initModelClasses(Request $request)
    {
        $this->project = MonitoringProject::findOrFail($request->input('projectId', null));

        $this->keywords = $this->project->keywords;

        $region = $this->project->searchengines();

        if($request->input('regionId'))
            $region->where('id', $request->input('regionId'));

        $this->region = $region->orderBy('id', 'asc')->first();

        $this->positions = $this->getPositionsForRange($request->input('dateRange', null));
    }

    public function getPositionsForRange($dateRange = null)
    {
        $model = new MonitoringPosition();
        $positions = $model->where('monitoring_searchengine_id', $this->region->id)
            ->whereIn('monitoring_keyword_id', $this->keywords->pluck('id'))
            ->dateRange($dateRange)->get();

        return $positions;
    }

    public function getLastPositionsByDays()
    {
        $positions = $this->positions->groupBy('date')->transform(function($item){
            return $item->sortByDesc('created_at')->unique('monitoring_keyword_id')->pluck('position');
        });

        return $positions;
    }

    public function getChartData(Request $request)
    {
        $this->initModelClasses($request);

        switch ($request->input('chart')){

            default:
                return $this->getTopPercent($request);

        }
    }

    protected function getTopPercent(Request $request)
    {
        $topSettings = [
            10 => ['top' => 10, 'color' => '#28a745'],
            20 => ['top' => 20, 'color' => '#007bff'],
            30 => ['top' => 30, 'color' => '#ffc107'],
            40 => ['top' => 40, 'color' => '#dc3545'],
            50 => ['top' => 50, 'color' => '#6c757d'],
        ];
        $response = [];
        $positions = $this->getLastPositionsByDays();
        foreach ($positions as $date => $position){

            $response['labels'][] = $date;
            foreach ($topSettings as $setting)
                $response['data'][$setting['top']][] = $this->calculatePercentPositionsInTop($position, $setting['top']);
        }

        $chart = new AreaChartData($response['labels']);
        foreach ($response['data'] as $top => $data)
        $chart->setBackgroundColor($topSettings[$top]['color'])->setBorderColor($topSettings[$top]['color'])->setLabel('% ключей в ТОП-' . $top)->setData($data);

        return $chart->get();
    }

    public function calculatePercentPositionsInTop(Collection $positions, $top)
    {
        $items = $positions->count();
        $count = $positions->filter(function ($val) use ($top){
            return $val <= $top;
        })->count();

        return round(($count / $items) * 100, 2);
    }
}
