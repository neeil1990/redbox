<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\TariffPay;

class DeleteTariffByUsers
{
    protected $user;
    protected $tariff;

    public function __construct(User $user, TariffPay $tariff)
    {
        $this->user = $user;
        $this->tariff = $tariff;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $tariffs = $this->tariff->active()->where('active_to', '<', \Carbon\Carbon::now())->get();

        foreach ($tariffs as $tariff){
            $class = new $tariff->class_tariff;
            $user = $this->user->find($tariff->user_id);

            $tariff->update(['status' => false]);

            $user->removeRole($class->code());

            $user->assignRole('Free');
        }

        return $next($request);
    }
}
