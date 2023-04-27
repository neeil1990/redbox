<?php


namespace App\Classes\Tariffs;


use App\Classes\Tariffs\Interfaces\Settings;
use App\Classes\Tariffs\Period\ThreeMonthsTariff;
use App\Classes\Tariffs\Settings\MaximumSettings;


class MaximumTariff extends Tariff
{
    public $name = 'Maximum';
    protected $code = 'Maximum';

    public function __construct()
    {
        parent::__construct(new ThreeMonthsTariff());

        $this->name = __('Maximum');

        $settings = $this->settings()->get();
        if(array_key_exists('price', $settings)){
            $this->setPrice($settings['price']['value']);
        }
    }

    private function setPrice(int $price)
    {
        $this->price = $price;
    }

    public function name(): string
    {
        return $this->name;
    }

    protected function settings(): Settings
    {
        return new MaximumSettings($this->code(), $this->user);
    }

    public function code(): string
    {
        return $this->code;
    }
}
