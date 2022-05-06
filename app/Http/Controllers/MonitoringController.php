<?php

namespace App\Http\Controllers;


use App\Classes\Position\PositionStore;
use App\Jobs\PositionQueue;
use App\MonitoringKeyword;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    protected $user;

    /**
     * ProfilesController constructor.
     */
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
        /** @var User $user */
        $user = $this->user;
        $projects = $user->monitoringProjects()->get();

        //$model = new MonitoringKeyword();
        //$query = $model->where('id', 9)->first();

        //$store = (new PositionStore($query, false))->save();
        //dispatch((new PositionQueue($query))->onQueue('position'));


        return view('monitoring.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('monitoring.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->create([
            'status' => 1,
            'name' => $request->input('name'),
            'url' => $request->input('url'),
        ]);

        $keywords = $request->input('keywords');
        foreach ($keywords['query'] as $ind => $query){

            $project->keywords()->create([
                'monitoring_group_id' => $request->input('group', null),
                'target' => $request->input('target', null),
                'query' => $query,
                'page' => $keywords['page'][$ind]
            ]);
        }

        $competitors = preg_split("/\r\n|\n|\r/", $request->input('competitors'));
        foreach ($competitors as $competitor){

            $project->competitors()->create([
                'url' => $competitor,
            ]);
        }

        $searches = $request->input('lr');
        foreach($searches as $engine => $dates){

            foreach ($dates as $data){

                $project->searchengines()->create([
                    'engine' => $engine,
                    'lr' => $data
                ]);
            }
        }

        return redirect()->route('monitoring.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->where('id', $id)->first();

        return view('monitoring.show', compact('project'));
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
        /** @var User $user */
        $user = $this->user;
        $user->monitoringProjects()->where('id', $id)->delete();
    }
}
