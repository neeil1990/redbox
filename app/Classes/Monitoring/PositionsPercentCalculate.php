<?php


namespace App\Classes\Monitoring;


use Illuminate\Support\Collection;

class PositionsPercentCalculate
{
    protected $positions;

    public function __construct(Collection $positions)
    {
        $this->positions = $positions;
    }

    public function top3()
    {
        return $this->calculate(3);
    }

    public function top5()
    {
        return $this->calculate(5);
    }

    public function top10()
    {
        return $this->calculate(10);
    }

    public function top30()
    {
        return $this->calculate(30);
    }

    public function top100()
    {
        return $this->calculate(100);
    }

    public function middle()
    {
        return ($this->positions->isNotEmpty()) ? round($this->positions->sum() / $this->positions->count()) : 0;
    }

    private function calculate($position)
    {
        return Helper::calculateTopPercentByPositions($this->positions, $position);
    }
}
