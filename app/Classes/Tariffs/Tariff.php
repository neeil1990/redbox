<?php


namespace App\Classes\Tariffs;


use App\Classes\Tariffs\Interfaces\Settings;

abstract class Tariff
{
    protected $responses;

    abstract public function name(): string;

    abstract public function settings(): Settings;

    public function get()
    {
        $this->responses['name'] = $this->name();

        $settings = $this->settings();
        $this->responses['settings'] = $settings->get();

        return $this->responses;
    }
}
