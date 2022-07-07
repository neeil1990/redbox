<?php

namespace App\Http\Controllers;

use App\Classes\Position\PositionStore;
use App\Jobs\PositionQueue;
use App\Location;
use App\MonitoringKeyword;
use App\MonitoringPosition;
use App\MonitoringProject;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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

    public function showControlsPanel()
    {
        return view('monitoring.keywords.controls');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $project = MonitoringProject::findOrFail($id);

        return view('monitoring.keywords.create', compact('project'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function store(Request $request)
    {
        $id = $request->input('monitoring_project_id');
        $queries = preg_split("/\r\n|\n|\r/", $request->input('query'));

        $project = MonitoringProject::findOrFail($id);

        foreach ($queries as $query) {

            $project->keywords()->create([
                'monitoring_group_id' => $request->input('monitoring_group_id'),
                'target' => $request->input('target'),
                'page' => $request->input('page'),
                'query' => $query,
            ]);
        }

        return $project;
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
        /** @var User $user */
        $user = $this->user;

        $keyword = MonitoringKeyword::findOrFail($id);
        if($keyword->project->user->id === $user->id){
            return view('monitoring.keywords.edit', compact('keyword'));
        }
        else
            return abort('404');
    }

    public function editPlural($id)
    {
        $project = MonitoringProject::findOrFail($id);
        return view('monitoring.keywords.edit_plural', compact('project'));
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
        /** @var User $user */
        $user = $this->user;

        $keyword = MonitoringKeyword::findOrFail($id);
        if($keyword->project->user->id === $user->id){

            $keyword->update($request->all());
            return $keyword;
        }
        else
            return abort('404');
    }

    public function updatePlural(Request $request)
    {
        $keywords = MonitoringKeyword::whereIn('id', $request->input('id', []))->update([
            'monitoring_group_id' => $request->input('monitoring_group_id'),
            'target' => $request->input('target'),
            'page' => $request->input('page'),
        ]);

        return $keywords;
    }

    protected function scanPosition($id)
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

    public function setTestPositions(Request $request, $id_project)
    {
        $project = MonitoringProject::findOrFail($id_project);
        $search = $request->input('search');
        $dates = explode(' - ', $request->input('date'));

        $startDate = Carbon::createFromFormat('Y-m-d', $dates[0]);
        $endDate = Carbon::createFromFormat('Y-m-d', $dates[1]);

        $dateRange = CarbonPeriod::create($startDate, $endDate)->toArray();

        $project->keywords->each(function($key) use ($search, $dateRange){

            foreach($dateRange as $date){

                factory(MonitoringPosition::class)->create([
                    'monitoring_keyword_id' => $key->id,
                    'monitoring_searchengine_id' => $search,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        });

        return redirect()->back();
    }

    public function showEmptyModal()
    {
        return view('monitoring.keywords.empty');
    }
}
