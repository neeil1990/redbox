<?php


namespace App\Classes\Monitoring\Widgets;


class WidgetsFactory
{
    protected $widgets;

    public function __construct()
    {
        $this->widgets = collect([]);
        $this->widgets->push(new ProjectCountWidget());
        $this->widgets->push(new TopTenPercentWidget());
        $this->widgets->push(new TopThirtyPercentWidget());
        $this->widgets->push(new TopOneHundredPercentWidget());
        $this->widgets->push(new MaxBudgetWidget());
        $this->widgets->push(new MasteredBudgetWidget());
        $this->widgets->push(new MasteredBudgetPercentWidget());
        $this->widgets->push(new ProjectManagerCountWidget());
        $this->widgets->push(new SeoUserCountWidget());
    }

    public function get()
    {
        return $this->widgets;
    }

    public function getCollection()
    {
        $widgets = collect([]);
        foreach ($this->widgets as $widget){
            if($widget->widget()->isNotEmpty())
                $widgets->push($widget->widget());
        }

        return $widgets;
    }

    public function getMenu()
    {
        $menu = collect([]);
        foreach ($this->widgets as $widget){
            $active = false;
            if($widget->widget()->isNotEmpty() && $widget->widget()->has('active'))
                $active = $widget->widget()->get('active');

            $menu->push(collect([
                'code' => $widget->getCode(),
                'name' => $widget->getName(),
                'active' => $active,
            ]));
        }

        return $menu;
    }

    public function getWidgetByCode(string $code)
    {
        foreach ($this->widgets as $widget)
            if($widget->getCode() === $code)
                return $widget;

        return null;
    }
}
