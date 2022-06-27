<?php

namespace App\Http\Controllers;

use App\Classes\Position\PositionStore;
use App\Jobs\PositionQueue;
use App\Location;
use App\MonitoringKeyword;
use App\MonitoringPosition;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringKeywordsController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->user;

        $location = new Location();
        $model = new MonitoringKeyword();
        $query = $model->where('id', $id)->first();

        if($query->project->user_id !== $user->id)
            abort(403);

        $positions = $query->positions()->get();

        $data = ['query' => [], 'positions' => []];

        $data['query'] = $query->toArray();

        foreach ($positions as $position){
            $engine = $position->engine;

            $region = $location->where('lr', $engine->lr)->first();

            if($region){
                $region = $region->toArray();

                $data['positions'][$engine->lr]['header'] = [
                    'region' => $region['name'],
                    'engine' => ucfirst($engine->engine),
                ];

                $data['positions'][$engine->lr]['item'][] = [
                    'id' => $position->id,
                    'engine' => $engine->engine,
                    'engine_id' => $position->monitoring_searchengine_id,
                    'position' => $position->position ?: '>100',
                    'created_at' => $position->created_at->format('d.m.Y H:m:s'),
                ];
            }
        }

        return view('monitoring.positions', compact('data'));
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
    public function update($id)
    {
        $model = new MonitoringKeyword();
        $query = $model->where('id', $id)->first();

        $store = (new PositionStore($query, true))->save();

        if($store)
            return redirect()->back();
    }

    public function addingQueue(Request $request)
    {
        $id = $request->input('id', null);
        $model = new MonitoringKeyword();
        $query = $model->where('id', $id)->first();

        dispatch((new PositionQueue($query))->onQueue('position'));

        return $query;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /** @var User $user */
        $user = $this->user;

        $keyword = MonitoringKeyword::findOrFail($id);
        if($keyword->project->user->id === $user->id)
            $keyword->delete();

        return $keyword;
    }
}
