<?php


namespace App\Classes\Monitoring;


use App\MonitoringKeywordPrice;
use App\MonitoringPosition;
use Illuminate\Support\Collection;

class MasteredPositions
{
    private $positions;
    private $modelPrice;

    public function __construct(Collection $positions)
    {
        $this->positions = $positions;
        $this->modelPrice = new MonitoringKeywordPrice();
    }

    public function percentOfDay($budget)
    {
        if(empty($budget))
            return null;

        return floor($this->total() / ($budget / 30) * 100);
    }

    public function percentOf($budget)
    {
        if(empty($budget))
            return null;

        return round(($this->total() / $budget) * 100, 2);
    }

    public function total()
    {
        $top1 = $this->top1();
        $top3 = $this->top3();
        $top5 = $this->top5();
        $top10 = $this->top10();
        $top20 = $this->top20();
        $top50 = $this->top50();
        $top100 = $this->top100();

        return array_sum([$top1['total'], $top3['total'], $top5['total'], $top10['total'], $top20['total'], $top50['total'], $top100['total']]);
    }

    public function top1()
    {
        $total = 0;
        $positions = $this->positions->where('position', 1);
        foreach ($positions as $position){
            if(!$price = $this->getPrice($position, $position['monitoring_searchengine_id']))
                continue;

            $total += $price['top1'];
        }

        return ['count' => $positions->count(), 'total' => $total];
    }

    public function top3()
    {
        return $this->range(2, 3);
    }
    public function top5()
    {
        return $this->range(4, 5);
    }

    public function top10()
    {
        return $this->range(6, 10);
    }

    public function top20()
    {
        return $this->range(11, 20);
    }
    public function top50()
    {
        return $this->range(21, 50);
    }
    public function top100()
    {
        return $this->range(51, 100);
    }

    private function range($start, $end)
    {
        $total = 0;
        $positions = $this->positions->whereBetween('position', [$start, $end]);
        foreach ($positions as $position){
            if(!$price = $this->getPrice($position, $position['monitoring_searchengine_id']))
                continue;

            $idx = 'top' . $end;
            if(isset($price[$idx]))
                $total += $price[$idx];
        }

        return ['count' => $positions->count(), 'total' => $total];
    }

    private function getPrice(MonitoringPosition $position, $region)
    {
        return $position->keyword->price()->where('monitoring_searchengine_id', $region)->first();
    }

}
