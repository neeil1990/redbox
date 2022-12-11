<?php

namespace App\Http\Controllers;

use App\Balance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($response = null)
    {
        $user = Auth::user();
        $balances = $user->balances()->orderBy('id', 'desc')->get();

        return view('balance.index', compact('balances', 'response'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
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

    public function countingMetrics(Request $request): JsonResponse
    {
        $balance = Balance::where('id', '=', $request->input('id'))
            ->where('user_id', '=', Auth::id())
            ->where('counting', '=', 0)->first();

        if (isset($balance)) {
            $balance->counting = 1;
            $balance->save();
            return response()->json([
                'click' => true
            ]);
        } else {
            return response()->json([
                'click' => false
            ]);
        }
    }
}
