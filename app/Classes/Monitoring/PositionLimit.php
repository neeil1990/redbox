<?php


namespace App\Classes\Monitoring;


use App\User;
use Carbon\Carbon;

class PositionLimit extends Limits
{
    private $name = "monitoring";

    public function __construct(int $user)
    {
        /** @var User $user */
        $user = User::find($user);
        $this->user = $user;

        $tariff = $user->tariff()->getAsArray();
        if(isset($tariff['settings'][$this->name])){
            $settings = $tariff['settings'][$this->name];
            $this->limit = $settings['value'];
        }

        $this->date = Carbon::now()->format($this->dateFormat);
    }

}
