<?php

namespace App\Http\Controllers;

use App\Classes\Tariffs\FreeTariff;
use App\Classes\Tariffs\MaximumTariff;
use App\Classes\Tariffs\OptimalTariff;
use App\TariffSettingValue;
use Illuminate\Http\Request;

class TariffSettingValuesController extends Controller
{
    protected $tariffs;

    public function __construct()
    {
        $this->tariffs = [
            new FreeTariff(),
            new OptimalTariff(),
            new MaximumTariff()
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $select = [];
        foreach ($this->tariffs as $tariff)
            $select[$tariff->code()] = $tariff->name();

        return view('tariff-setting-values.create', compact('select'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tariff = $request->input('tariff');
        $id = $request->input('tariff_setting_id');

        if(TariffSettingValue::where(['tariff' => $tariff, 'tariff_setting_id' => $id])->count() > 0){
            TariffSettingValue::where(['tariff' => $tariff, 'tariff_setting_id' => $id])->first()->update($request->all());
        }else{
            TariffSettingValue::create($request->all());
        }

        return redirect()->route('tariff-settings.index');
    }

    /**
     * @param TariffSettingValue $settingValue
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     * @throws \Exception
     */
    public function destroy(TariffSettingValue $settingValue)
    {
        $settingValue->delete();
        return redirect('tariff-settings');
    }
}
