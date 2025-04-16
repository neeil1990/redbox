<?php


namespace App\Classes\Tariffs\Facades;


use App\Classes\Tariffs\FreeTariff;
use App\Classes\Tariffs\Interfaces\Period;
use App\Classes\Tariffs\MaximumTariff;
use App\Classes\Tariffs\OptimalTariff;
use App\Classes\Tariffs\Period\FiveDaysTariff;
use App\Classes\Tariffs\Period\OneDayTariff;
use App\Classes\Tariffs\Period\OneMonthsTariff;
use App\Classes\Tariffs\Period\PeriodTariff;
use App\Classes\Tariffs\Period\SixMonthsTariff;
use App\Classes\Tariffs\Period\ThreeMonthsTariff;
use App\Classes\Tariffs\Period\TwelveMonthsTariff;
use App\Classes\Tariffs\Tariff;
use App\Classes\Tariffs\UltimateTariff;
use App\User;
use Illuminate\Support\Facades\Auth;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use \Spatie\Permission\PermissionRegistrar;

class Tariffs
{
    private $tariffs = [];
    private $periods = [];

    public function __construct()
    {
        // all of tariffs
        $this->setTariffs(new OptimalTariff());
        $this->setTariffs(new UltimateTariff());
        $this->setTariffs(new MaximumTariff());

        // all of periods
        $this->setPeriods(new OneMonthsTariff());
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
     * @param Tariff $tariffs
     * @return void
     */
    public function setTariffs(Tariff $tariffs): void
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
    public function setPeriods(Period $periods): void
    {
        $this->periods[] = $periods;
    }

    public function getTariffByUser(User $user)
    {
        if(!$user)
            throw new ClassNotFoundException("Auth class not found!", Auth::class);

        app(PermissionRegistrar::class)->setPermissionsTeamId(1);

        $user->unsetRelation('roles');

        /** @var Tariff $tariff */
        foreach ($this->getTariffs() as $tariff){
            if($user->hasRole($tariff->code())){
                $tariff->setUser($user);
                return $tariff;
            }
        }

        if($user->hasRole('Free')){
            $free = new FreeTariff();
            $free->setUser($user);
            return $free;
        }

        return null;
    }

    public function getTariffByCode(string $code): ?Tariff
    {
        /** @var Tariff $tariff */
        foreach ($this->getTariffs() as $tariff){
            if($tariff->code() === $code)
                return $tariff;
        }

        return null;
    }

    public function getPeriodByCode(string $code): ?PeriodTariff
    {
        /** @var PeriodTariff $period */
        foreach ($this->getPeriods() as $period){
            if($period->code() === $code)
                return $period;
        }

        return null;
    }
}
