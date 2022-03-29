<?php

namespace App\Http\Controllers;

use App\Balance;
use App\Classes\Pay\Robokassa\RobokassaPay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function pays(Request $request)
    {
        $params = $request->all();
        $inv_id = $params['InvId'];
        $out_summ = $params['OutSum'];
        $password = $this->robokassa->getPassword();

        $crc = strtoupper($params['SignatureValue']);
        $my_crc = strtoupper(md5("$out_summ:$inv_id:$password"));

        if ($my_crc != $crc)
            return redirect()->route('balance-add.index');

        $pay = Auth::user()->balances()->where('id', $inv_id);
        if($pay->count()){

            $result = $pay->update([
                'source' => __('ROBOKASSA'),
                'status' => 1
            ]);

            if($result){
                $this->addBalanceUser($pay->first());
                return redirect()->route('balance.index');
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

        return redirect($this->robokassa->pays());
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
