<?php

namespace App\Http\Controllers;

use App\Classes\Tariffs\Facades\Tariffs;
use App\Classes\Tariffs\Interfaces\Period;
use App\Classes\Tariffs\Tariff;
use App\TariffSetting;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Prophecy\Exception\Doubler\ClassNotFoundException;

class TariffPayController extends Controller
{
    protected $user;
    protected $active;
    protected $tariffs = [];
    protected $periods = [];
    protected $select = [];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            if ($this->isSubscribe())
                $this->active = $this->user->pay()->active()->first();

            return $next($request);
        });

        $tariff = new Tariffs();
        $this->tariffs = $tariff->getTariffs();
        $this->periods = $tariff->getPeriods();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $actual = collect();
        if ($this->isSubscribe()) {
            $model = $this->active;
            $tariff = new $model->class_tariff;
            $actual->put('info', [
                ['title' => __('Tariff'), 'value' => $tariff->name()],
                ['title' => __('Days left'), 'value' => $model->active_to->diffInDays()],
                ['title' => __('Active to'), 'value' => $model->active_to->toDayDateTimeString()]
            ]);

            $actual->put('data', $model);
        }

        $total = $this->getTotal();

        foreach ($this->tariffs as $t)
            $this->select['tariffs'][$t->code()] = $t->name();

        foreach ($this->periods as $code => $p)
            $this->select['periods'][$p->code()] = $p->name();

        $select = $this->select;

        $tariffs = new Tariffs();
        $tariffsArray = [];
        foreach ($tariffs->getTariffs() as $tariff) {
            $tariffsArray[] = $tariff->getAsArray();
        }

        return view('tariff.index', compact('select', 'total', 'actual', 'tariffsArray'));
    }

    public function total(Request $request)
    {
        $name = $request->input('name');
        $period = $request->input('period');

        return $this->getTotal($name, $period);
    }

    protected function getTotal(string $name = null, string $period = null)
    {
        if (is_null($name) && is_null($period)) {
            $tariff = Arr::first($this->tariffs);
            $tariff->setPeriod(Arr::first($this->periods));
        } else {
            $tariff = $this->getTariff($name);
            $tariff->setPeriod($this->getPeriod($period));
        }

        return collect([
            'name' => ['title' => __('Tariff'), 'value' => $tariff->name()],
            'days' => ['title' => __('Days'), 'value' => $tariff->getPeriod()->days()],
            'price' => ['title' => __('Price'), 'value' => $tariff->price('price')],
            'discount' => ['title' => __('Discount'), 'value' => $tariff->price('discount')],
            'total' => ['title' => __('Total'), 'value' => $tariff->price('priceWithDiscount')]
        ]);
    }

    /**
     * @param string $code
     * @return Tariff
     */
    protected function getTariff(string $code): Tariff
    {
        foreach ($this->tariffs as $tariff) {
            if ($tariff->code() === $code)
                return $tariff;
        }

        throw new ClassNotFoundException("Tariffs not found!", Tariff::class);
    }

    /**
     * @param string $code
     * @return Period
     */
    protected function getPeriod(string $code): Period
    {
        foreach ($this->periods as $period) {
            if ($period->code() === $code)
                return $period;
        }

        throw new ClassNotFoundException("Period's tariff not found!", Period::class);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function isSubscribe()
    {
        return $this->user->pay()->active()->count() ? true : false;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->isSubscribe()) {
            Session::flash('error', __('Subscribe has already activated!'));
            return redirect()->route('tariff.index');
        }

        $tariff = $this->getTariff($request->input('tariff'));
        $tariff->setPeriod($this->getPeriod($request->input('period')));

        try {
            $this->user->decrement('balance', $tariff->price('priceWithDiscount'));
        } catch (QueryException $exception) {
            Session::flash('error', __('Replenish the balance!'));
            return redirect()->route('tariff.index');
        }

        $this->user->pay()->create([
            'status' => true,
            'class_tariff' => get_class($tariff),
            'class_period' => get_class($tariff->getPeriod()),
            'sum' => $tariff->price('priceWithDiscount'),
            'active_to' => Carbon::now()->addDays($tariff->getPeriod()->days())
        ]);

        $this->user->balances()->create([
            'sum' => $tariff->price('priceWithDiscount'),
            'source' => "Оплата тарифа " . $tariff->name(),
            'status' => 2
        ]);

        $tariff->assignRole();

        return redirect()->route('tariff.index');
    }

    public function unsubscribe()
    {
        $tariff = $this->calculateCostDaysByActiveTariff();

        $cost = $tariff->price();

        $this->user->increment('balance', $cost['priceWithDiscount']);

        $this->user->balances()->create([
            'sum' => $cost['priceWithDiscount'],
            'source' => "Возврат средств при смене тарифа " . $tariff->name(),
            'status' => 1
        ]);

        $tariff->removeRole();

        $this->active->update(['status' => false]);
    }

    public function confirmUnsubscribe($confirm = null)
    {
        if ($confirm === "confirm") {
            $tariff = $this->calculateCostDaysByActiveTariff();

            return collect([
                'name' => $tariff->name(),
                'prices' => $tariff->price(),
                'active_days' => $tariff->getPeriod()->days()
            ]);
        }

        if ($confirm === "canceled") {
            $this->unsubscribe();
            Session::flash('info', __('Subscribe has been canceled!'));
        }
    }

    /**
     * @param int|null $days
     * @return Tariff|null
     */
    public function calculateCostDaysByActiveTariff(int $days = null): ?Tariff
    {
        if (!$this->isSubscribe())
            return null;

        $model = $this->active;

        /** @var Tariff $tariff */
        $tariff = new $model->class_tariff;

        /** @var Period $period */
        $period = new $model->class_period;

        if (empty($days))
            $days = $model->active_to->diffInDays();

        $period->setMonths(0);
        $period->setDays($days);

        $tariff->setPeriod($period);

        return $tariff;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
