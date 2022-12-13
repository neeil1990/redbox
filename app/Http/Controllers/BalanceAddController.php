<?php

namespace App\Http\Controllers;

use App\Balance;
use App\Classes\Pay\Robokassa\RobokassaPay;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BalanceAddController extends Controller
{
    private $robokassa;

    public function __construct()
    {
        $this->robokassa = new RobokassaPay();

        $this->robokassa->setParams('IsTest', 0);
        $this->robokassa->setParams('Description', 'Redbox.su');
    }

    /**
     * @param Request $request
     */
    public function result(Request $request)
    {
        $params = $request->all();

        if (!$this->robokassa->checkOut($params)) {
            echo "bad sign\n";
            exit();
        }

        $invId = $params["InvId"];

        $balance = Balance::where('id', $invId);
        if ($balance->count()) {

            $result = $balance->update([
                'source' => $params["PaymentMethod"],
                'status' => 1
            ]);

            if ($result) {
                $this->addBalanceToUser($balance->first());
                echo "OK$invId\n";
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
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
        $this->robokassa->setParams('Receipt', urlencode('{"items":[{"name":"Доступ к ПО сервиса RedBox.su","quantity":"1","sum":"'.$sum.'","tax":"none"}]}'));

        return redirect($this->robokassa->action());
    }

    protected function addBalanceToUser(Balance $balance)
    {
        $user = User::find($balance->user_id);
        $user->increment('balance', $balance->sum);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
