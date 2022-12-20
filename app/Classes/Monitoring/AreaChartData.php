<?php


namespace App\Classes\Monitoring;


class AreaChartData
{
    public $label = "Chart";
    public $fill = false;
    public $backgroundColor = "#1976d2";
    public $borderColor = "#1976d2";
    public $hidden = false;

    protected $labels;
    protected $datasets;

    public function __construct(array $labels)
    {
        if(is_array($labels))
            $labels = collect($labels)->values();

        $this->labels = $labels;
    }

    public function get()
    {
        return [
            'labels' => $this->labels,
            'datasets' => $this->datasets,
        ];
    }

    /**
     * @param mixed $data
     * @return AreaChartData
     */
    public function setData(array $data)
    {
        $this->datasets[] = [
            'data' => $data,
            'label' => $this->label,
            'fill' => $this->fill,
            'backgroundColor' => $this->backgroundColor,
            'borderColor' => $this->borderColor,
            'hidden' => $this->hidden,
        ];

        return $this;
    }

    /**
     * @param string $label
     * @return AreaChartData
     */
    public function setLabel(string $label): AreaChartData
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param bool $fill
     * @return AreaChartData
     */
    public function setFill(bool $fill): AreaChartData
    {
        $this->fill = $fill;
        return $this;
    }

    /**
     * @param string $backgroundColor
     * @return AreaChartData
     */
    public function setBackgroundColor($backgroundColor): AreaChartData
    {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    /**
     * @param string $borderColor
     * @return AreaChartData
     */
    public function setBorderColor(string $borderColor): AreaChartData
    {
        $this->borderColor = $borderColor;
        return $this;
    }

    /**
     * @param string $pointHoverRadius
     * @return AreaChartData
     */
    public function setPointHoverRadius(string $pointHoverRadius): AreaChartData
    {
        $this->pointHoverRadius = $pointHoverRadius;
        return $this;
    }

    /**
     * @param string $pointRadius
     * @return AreaChartData
     */
    public function setPointRadius(string $pointRadius): AreaChartData
    {
        $this->pointRadius = $pointRadius;
        return $this;
    }

    /**
     * @param bool $hidden
     * @return AreaChartData
     */
    public function setHidden(bool $hidden): AreaChartData
    {
        $this->hidden = $hidden;
        return $this;
    }



}
