<?php


namespace App\Classes\Tariffs;


class Tariffs
{

    public static function get()
    {
        $user = auth()->user();

        $tariff = null;

        if($user->hasRole('Free'))
            $tariff = (new FreeTariff())->get();
        elseif($user->hasRole('Optimal'))
            $tariff = (new OptimalTariff())->get();
        elseif($user->hasRole('Maximum'))
            $tariff = (new OptimalTariff())->get();

        return $tariff;
    }
}
