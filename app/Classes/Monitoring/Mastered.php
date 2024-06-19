<?php


namespace App\Classes\Monitoring;


use App\MonitoringKeywordPrice;
use Illuminate\Support\Collection;

class Mastered
{
    protected $positions;
    protected $price;

    private $top1;
    private $top3;
    private $top5;
    private $top10;
    private $top20;
    private $top50;
    private $top100;

    public function __construct(Collection $positions)
    {
        $this->price = new MonitoringKeywordPrice;
        $this->positions = $positions;

        foreach ($this->positions as $position)
        {
            if(isset($position->monitoring_keyword_id))
                $position->query_id = $position->monitoring_keyword_id;

            if(isset($position->monitoring_searchengine_id))
                $position->engine_id = $position->monitoring_searchengine_id;
        }
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
        if($this->top1)
            return $this->top1;

        $positions = $this->positions->where('position', 1);

        $price = $this->calcPrice($positions, 'top1');

        $this->top1 = ['count' => $positions->count(), 'total' => $price];

        return $this->top1;
    }

    public function top3()
    {
        if($this->top3)
            return $this->top3;

        $this->top3 = $this->range(2, 3);

        return $this->top3;
    }

    public function top5()
    {
        if($this->top5)
            return $this->top5;

        $this->top5 = $this->range(4, 5);

        return $this->top5;
    }

    public function top10()
    {
        if($this->top10)
            return $this->top10;

        $this->top10 = $this->range(6, 10);

        return $this->top10;
    }

    public function top20()
    {
        if($this->top20)
            return $this->top20;

        $this->top20 = $this->range(11, 20);

        return $this->top20;
    }

    public function top50()
    {
        if($this->top50)
            return $this->top50;

        $this->top50 = $this->range(21, 50);

        return $this->top50;
    }

    public function top100()
    {
        if($this->top100)
            return $this->top100;

        $this->top100 = $this->range(51, 100);

        return $this->top100;
    }

    private function range($start, $end)
    {
        $positions = $this->positions->whereBetween('position', [$start, $end]);

        $price = $this->calcPrice($positions, 'top' . $end);

        return ['count' => $positions->count(), 'total' => $price];
    }

    private function calcPrice($positions, $field)
    {
        $price = 0;

        foreach($positions as $position)
            $price += $this->getPrice($position['query_id'], $position['engine_id'], $field);

        return $price;
    }

    private function getPrice($queryId, $engineId, $value)
    {
        return $this->price->where('monitoring_keyword_id', $queryId)->where('monitoring_searchengine_id', $engineId)->value($value);
    }

}
