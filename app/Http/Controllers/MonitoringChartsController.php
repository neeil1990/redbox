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

            case "regions_middle":
                return $this->getMiddlePositionAllRegions($request);
                break;

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

        $response["data"][0] = $this->calculatePercentPositionsInTop($positions, 3);
        $response["data"][1] = $this->calculatePercentPositionsInTop($positions, 10);
        $response["data"][2] = $this->calculatePercentPositionsInTop($positions, 30);
        $response["data"][3] = $this->calculatePercentPositionsInTop($positions, 50);
        $response["data"][4] = $this->calculatePercentPositionsInTop($positions, 100);

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

    protected function getMiddlePositionAllRegions(Request $request)
    {
        if($this->project->searchengines->count() <= 1)
            return false;

        $response = [];

        foreach($this->project->searchengines as $engine){

            $this->region = $engine;
            $this->positions = $this->getPositionsForRange($request->input('dateRange', null));
            $positions = $this->getLastPositions($request->input('range'));

            if($positions->isEmpty())
                return (new AreaChartData([]))->setData([])->get();

            foreach ($positions as $label => $position){

                $response['labels'][] = $label;
                $response['data'][$engine->lr][$label] = round($position->sum() / $position->count());
            }
        }
        $response['labels'] = array_unique($response['labels']);

        foreach($response['labels'] as $label){

            foreach ($response['data'] as &$data){
                if(!array_key_exists($label, $data))
                    $data[$label] = null;
            }
        }

        $chart = new AreaChartData($response['labels']);

        foreach ($response['data'] as $lr => &$data){

            $order = $response['labels'];
            uksort($data, function($key1, $key2) use ($order) {
                return ((array_search($key1, $order) > array_search($key2, $order)) ? 1 : -1);
            });

            $region = $this->project->searchengines->where('lr', $lr)->first();
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));

            $chart->setBackgroundColor($color)
                ->setHidden(false)
                ->setBorderColor($color)
                ->setLabel($region->location->name . ' [' . $lr . ']')
                ->setData($data);
        }

        return $chart->get();
    }

    protected function getTopPercent(Request $request)
    {
        $topSettings = [
            1 => ['top' => 1, 'color' => '#228B22', 'hidden' => true],
            3 => ['top' => 3, 'color' => '#1E90FF', 'hidden' => true],
            5 => ['top' => 5, 'color' => '#008080', 'hidden' => true],
            10 => ['top' => 10, 'color' => '#9370DB', 'hidden' => false],
            20 => ['top' => 20, 'color' => '#D2691E', 'hidden' => true],
            30 => ['top' => 30, 'color' => '#CD5C5C', 'hidden' => true],
            40 => ['top' => 40, 'color' => '#B22222', 'hidden' => true],
            50 => ['top' => 50, 'color' => '#A9A9A9', 'hidden' => true],
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
        $filtered = collect([]);

        $currentWeek = null;
        foreach($positionByDays as $date => $positions){

            $week = Carbon::parse($date)->week();
            if($currentWeek === null || $currentWeek !== $week)
                $filtered->put($date, $positions);

            $currentWeek = $week;
        }

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
