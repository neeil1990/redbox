<?php


namespace App\Classes\Tariffs;


use App\Classes\Tariffs\Interfaces\Settings;
use App\Classes\Tariffs\Period\ThreeMonthsTariff;
use App\Classes\Tariffs\Settings\FreeSettings;

class FreeTariff extends Tariff
{
    public $name = 'Free';
    protected $code = 'Free';

    public function __construct()
    {
        parent::__construct(new ThreeMonthsTariff());

        $this->name = __('Free');

        $settings = $this->settings()->get();
        if(array_key_exists('price', $settings)){
            $this->setPrice($settings['price']['value']);
        }
    }

    private function setPrice(int $price)
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return Settings
     */
    protected function settings(): Settings
    {
        return new FreeSettings($this->code(), $this->user);
    }

    public function code(): string
    {
        return $this->code;
    }
}
