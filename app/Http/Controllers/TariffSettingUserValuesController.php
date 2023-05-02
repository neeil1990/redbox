<?php

namespace App\Http\Controllers;

use App\TariffSetting;
use App\TariffSettingUserValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TariffSettingUserValuesController extends Controller
{
    protected $settings;

    public function __construct()
    {
        $this->middleware(['role:Super Admin|admin']);

        $this->settings = TariffSetting::get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $settings = $this->settings->pluck('name', 'id');
        return view('profile.tariff.create', compact('settings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $settings = $this->settings->find($request['settings']);
        if(!$settings)
            return abort('404');

        $user = Auth::id();
        $fields = $request->input('fields', []);

        foreach ($fields as $id => $field){
            if(!$field)
                continue;

            TariffSettingUserValue::updateOrCreate(
                ['tariff_setting_value_id' => $id, 'user_id' => $user],
                ['value' => $field]
            );
        }

        return redirect()->route('profile.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $settings = $this->settings->find($id);
        return view('profile.tariff.partials._fields', ['fields' => $settings->fields->sortBy('sort')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ids = explode(',', $id);
        foreach ($ids as $id)
            TariffSettingUserValue::find($id)->delete();

        return redirect()->route('profile.index');
    }
}
