<?php


namespace App\Classes\Tariffs\Facades;


use App\Classes\Tariffs\FreeTariff;
use App\Classes\Tariffs\Interfaces\Period;
use App\Classes\Tariffs\MaximumTariff;
use App\Classes\Tariffs\OptimalTariff;
use App\Classes\Tariffs\Period\FiveDaysTariff;
use App\Classes\Tariffs\Period\OneDayTariff;
use App\Classes\Tariffs\Period\SixMonthsTariff;
use App\Classes\Tariffs\Period\ThreeMonthsTariff;
use App\Classes\Tariffs\Period\TwelveMonthsTariff;
use App\Classes\Tariffs\Tariff;
use Illuminate\Support\Facades\Auth;
use Prophecy\Exception\Doubler\ClassNotFoundException;

class Tariffs
{
    private $tariffs = [];
    private $periods = [];

    public function __construct()
    {
        // all of tariffs
        $this->setTariffs(new FreeTariff());
        $this->setTariffs(new OptimalTariff());
        $this->setTariffs(new MaximumTariff());

        // all of periods
        $this->setPeriods(new OneDayTariff());
        $this->setPeriods(new FiveDaysTariff());
        $this->setPeriods(new ThreeMonthsTariff());
        $this->setPeriods(new SixMonthsTariff());
        $this->setPeriods(new TwelveMonthsTariff());
    }

    /**
     * @return array
     */
    public function getTariffs(): array
    {
        return $this->tariffs;
    }

    /**
     * @param array $tariffs
     */
    private function setTariffs(Tariff $tariffs): void
    {
        $this->tariffs[] = $tariffs;
    }

    /**
     * @return array
     */
    public function getPeriods(): array
    {
        return $this->periods;
    }

    /**
     * @param array $periods
     */
    private function setPeriods(Period $periods): void
    {
        $this->periods[] = $periods;
    }

    public function getTariffByUser()
    {
        $user = auth()->user();
        if(!$user)
            throw new ClassNotFoundException("Auth class not found!", Auth::class);

        /** @var Tariff $tariff */
        foreach ($this->getTariffs() as $tariff){
            if($user->hasRole($tariff->code()))
                return $tariff;
        }

        return null;
    }
}
