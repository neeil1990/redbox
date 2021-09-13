<?php

namespace App\Http\Controllers;

use App\Services;
use App\ServicesPositions;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\VarDumper\VarDumper;

class ProjectPositionController extends Controller
{
    public function index()
    {
//        $services = Services::get();
//        $positions = ServicesPositions::where('user_id', Auth::id())->first('positions');
//        $positionsCollect = new Collection(explode(' ', $positions->positions));
//        $sortServices = new Collection();
//        foreach ($services as $service) {
//            foreach ($positionsCollect as $position) {
//                if ($service['id'] == $position) {
//
//                }
////                if ($service['id'] === $position) {
////                    $sortServices->push($service);
////                }
//            }
//        }
//        dd(123);
////        foreach ($services as $service)
////        dd($positions);
////        $services->sort($positions);
////        dd($services);

        return view('test');
    }

    public function update(Request $request)
    {
//        $ids = '';
//        $positions = new Collection(\request('positions'));
//        foreach ($positions as $position) {
//            $ids .= $position['id'] . ' ';
//        }
//        $ids = mb_substr($ids, 0, -1);
//        $servicePosition = ServicesPositions::firstOrNew(['user_id' => Auth::id()]);
//        $servicePosition->positions = $ids;
//        $servicePosition->save();

//        $user->name = request('name');
//
//        $user->save()
//        Log::debug('arr', [$ids]);
//        $user = ServicesPositions::firstOrNew(['positions' =>  request('email')]);
//
//        $user->name = request('name');
//
//        $user->save();
//
//        $position = ServicesPositions::where('user_id', Auth::id())->get();
//        $services = Services::all();
//
//        foreach ($services as $service) {
//            foreach ($request->order as $order) {
//                if ($order['id'] == $service->id) {
//                    $service->update(['order' => $order['position']]);
//                }
//            }
//        }

        return response('Update Successfully.', 200);
    }
}
