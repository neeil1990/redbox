<?php

namespace App\Http\Controllers;

use App\Balance;
use App\Classes\Pay\Robokassa\RobokassaPay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BalanceAddController extends Controller
{
    private $robokassa;

    public function __construct()
    {
        $this->robokassa = new RobokassaPay();

        $this->robokassa->setParams('IsTest', 1);
        $this->robokassa->setParams('Desc', 'Redbox.su');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('balance-add.index');
    }

    /**
     * @param Request $request
     */
    public function result(Request $request)
    {
        $params = $request->all();

        Log::debug($params);

        if(!$this->robokassa->checkOut($params)){
            echo "bad sign\n";
            exit();
        }

        $invId = $params["InvId"];
        $pay = Auth::user()->balances()->where('id', $invId);
        if($pay->count()){

            $result = $pay->update([
                'source' => $params["PaymentMethod"],
                'status' => 1
            ]);

            if($result){
                $this->addBalanceUser($pay->first());
                echo "OK$invId\n";
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'sum' => ['required', 'min:1'],
        ]);

        $sum = $request->input('sum');

        $balance = Auth::user()->balances()->create([
            'sum' => $sum,
            'source' => __('Unknown source'),
            'status' => 0
        ]);

        $this->robokassa->setParams('InvId', $balance->id);
        $this->robokassa->setParams('OutSum', $sum);

        return redirect($this->robokassa->action());
    }

    protected function addBalanceUser(Balance $balance)
    {
        $user = Auth::user();
        $user->increment('balance', $balance->sum);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
