<?php


namespace App\Classes\Tariffs;


use App\Classes\Tariffs\Interfaces\Period;
use App\Classes\Tariffs\Interfaces\Settings;

abstract class Tariff
{
    protected $responses;
    protected $price = 0;
    protected $period;

    public function __construct(Period $period)
    {
        $this->period = $period;
    }

    abstract public function name(): string;

    abstract public function code(): string;

    public function price($field = null)
    {
        $period = $this->period->setPrice($this->priceForDay());

        $collection = collect([
            'price' => $period->price(),
            'priceWithDiscount' => $period->total(),
            'percent' => $period->percent(),
            'discount' => $period->discount(),
        ]);

        if(!is_null($field = $collection->get($field)))
            return $field;
        else
            return $collection;
    }

    public function priceForDay()
    {
        return $this->price;
    }

    abstract public function settings(): Settings;

    public function getAsArray()
    {
        $this->responses['name'] = $this->name();

        $settings = $this->settings();
        $this->responses['settings'] = $settings->get();

        return $this->responses;
    }

    public function setPeriod(Period $period)
    {
        $this->period = $period;
    }

    public function getPeriod()
    {
        return $this->period;
    }

    public function assignRole()
    {
        $user = auth()->user();
        $user->assignRole($this->code());
    }

    public function removeRole()
    {
        $user = auth()->user();
        $user->removeRole($this->code());
    }
}
