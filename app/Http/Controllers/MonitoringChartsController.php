<?php

namespace App\Http\Controllers;

use App\Classes\Monitoring\AreaChartData;
use App\MonitoringPosition;
use App\MonitoringProject;
use Carbon\Carbon;
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
        if($dateRange)
            $dateRange = explode(' - ', $dateRange);

        $model = new MonitoringPosition();
        $positions = $model->where('monitoring_searchengine_id', $this->region->id)
            ->whereIn('monitoring_keyword_id', $this->keywords->pluck('id'))
            ->dateRange($dateRange)
            ->get();

        return $positions;
    }

    public function getLastPositionsByDays()
    {
        $positions = $this->positions->groupBy('date')->transform(function($item){
            return $item->sortByDesc('created_at')->unique('monitoring_keyword_id')->pluck('position');
        })->sortBy(function ($product, $key) {

            return strtotime($key);
        });

        return $positions;
    }

    public function getChartData(Request $request)
    {
        $this->initModelClasses($request);

        switch ($request->input('chart')){

            case "middle":
                return $this->getMiddlePosition($request);
                break;

            case "distribution":
                return $this->getDistributionByTop($request);
                break;

            default:
                return $this->getTopPercent($request);

        }
    }

    protected function getDistributionByTop(Request $request)
    {
        $response = [
            'labels' => ['1-3', '1-10', '11-30', '31-50', '51-100', '101+'],
            'data' => [0, 0, 0, 0, 0, 0],
        ];
        $positionsGroupByDate = $this->getLastPositionsByDays();

        if($positionsGroupByDate->isEmpty())
            return (new AreaChartData([]))->setData([])->get();

        $positions = $positionsGroupByDate->last();
        foreach ($positions as $position){

            if($position > 0 && $position <= 3)
                $response['data'][0] += 1;

            if($position > 0 && $position <= 10)
                $response['data'][1] += 1;

            if($position > 10 && $position <= 30)
                $response['data'][2] += 1;

            if($position > 30 && $position <= 50)
                $response['data'][3] += 1;

            if($position > 50 && $position <= 100)
                $response['data'][4] += 1;
        }

        $response['data'][5] += ($this->keywords->count() - $positions->count());

        $chart = new AreaChartData($response['labels']);
        $chart->setBackgroundColor(['rgb(46, 150, 221)', 'rgb(33, 147, 108)', 'rgb(26, 188, 156)', 'rgb(162, 223, 159)', 'rgb(176, 199, 199)', 'rgb(251, 192, 45)'])
            ->setBorderColor('#FFFFFF')
            ->setHidden(false)
            ->setLabel('Распределение по ТОП-100')
            ->setData($response['data']);

        return $chart->get();
    }

    protected function getMiddlePosition(Request $request)
    {
        $response = [];
        $positions = $this->getLastPositions($request->input('range'));

        if($positions->isEmpty())
            return (new AreaChartData([]))->setData([])->get();

        foreach ($positions as $date => $position){

            $response['labels'][] = $date;
            $response['data'][] = round($position->sum() / $position->count());
        }

        $chart = new AreaChartData($response['labels']);
        $chart->setBackgroundColor('#28a745')
            ->setHidden(false)
            ->setBorderColor('#28a745')
            ->setLabel('Средняя позиция')
            ->setData($response['data']);

        return $chart->get();
    }

    protected function getTopPercent(Request $request)
    {
        $topSettings = [
            10 => ['top' => 10, 'color' => '#28a745', 'hidden' => false],
            20 => ['top' => 20, 'color' => '#007bff', 'hidden' => false],
            30 => ['top' => 30, 'color' => '#ffc107', 'hidden' => true],
            40 => ['top' => 40, 'color' => '#dc3545', 'hidden' => true],
            50 => ['top' => 50, 'color' => '#6c757d', 'hidden' => true],
        ];
        $response = [];
        $positions = $this->getLastPositions($request->input('range'));

        if($positions->isEmpty())
            return (new AreaChartData([]))->setData([])->get();

        foreach ($positions as $date => $position){

            $response['labels'][] = $date;
            foreach ($topSettings as $setting)
                $response['data'][$setting['top']][] = $this->calculatePercentPositionsInTop($position, $setting['top']);
        }

        $chart = new AreaChartData($response['labels']);
        foreach ($response['data'] as $top => $data)
        $chart->setBackgroundColor($topSettings[$top]['color'])
            ->setHidden($topSettings[$top]['hidden'])
            ->setBorderColor($topSettings[$top]['color'])
            ->setLabel('% ключей в ТОП-' . $top)
            ->setData($data);

        return $chart->get();
    }

    public function getLastPositions(string $range = null)
    {
        $positions = $this->getLastPositionsByDays();

        if($range == 'weeks')
            $positions = $this->getLastPositionsByWeeks($positions);

        if($range == 'month')
            $positions = $this->getLastPositionsByMonths($positions);

        return $positions;
    }

    protected function getLastPositionsByWeeks(Collection $positionByDays)
    {
        $days = -1;
        $filtered = $positionByDays->filter(function () use (&$days) {
            $days++;
            return !($days % 7);
        });

        return $filtered;
    }

    protected function getLastPositionsByMonths(Collection $positionByDays)
    {
        $unique = $positionByDays->unique(function ($item, $key) {
            $carbon = Carbon::parse($key);
            return $carbon->format('m.Y');
        });

        return $unique;
    }

    public function calculatePercentPositionsInTop(Collection $positions, $top)
    {
        $items = $this->keywords->count();
        $count = $positions->filter(function ($val) use ($top){
            return $val <= $top;
        })->count();

        return round(($count / $items) * 100, 1);
    }
}
