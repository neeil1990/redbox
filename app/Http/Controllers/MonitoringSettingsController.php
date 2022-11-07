<?php

namespace App\Http\Controllers;

use App\MonitoringSettings;
use Illuminate\Http\Request;

class MonitoringSettingsController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->middleware(['role:Super Admin|admin']);

        $this->model = new MonitoringSettings();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateOrCreate(Request $request)
    {
        foreach ($request->all() as $key => $item){
            if($key == '_token')
                continue;

           $this->model->updateOrCreate(
                ['name' => $key],
                ['value' => $item]
            );
        }

        flash()->overlay(__('Settings updated'), __('Save success!'))->success();

        return redirect()->back();
    }

    public function destroy($name)
    {
        if($this->model->where('name', $name)->delete())
            flash()->overlay($name .' '. __('deleted'), __('Delete success!'))->success();

        return redirect()->back();
    }
}
