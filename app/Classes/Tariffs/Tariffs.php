<?php


namespace App\Classes\Tariffs;


class Tariffs
{

    public static function get()
    {
        $user = auth()->user();

        $tariff = null;

        if($user->hasRole('Free'))
            $tariff = (new FreeTariff())->getAsArray();
        elseif($user->hasRole('Optimal'))
            $tariff = (new OptimalTariff())->getAsArray();
        elseif($user->hasRole('Maximum'))
            $tariff = (new OptimalTariff())->getAsArray();

        return $tariff;
    }
}
