<?php

namespace App\Http\Controllers;


use App\Classes\Monitoring\ProjectDataTable;
use App\Classes\Position\PositionStore;
use App\Jobs\PositionQueue;
use App\MonitoringKeyword;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        //$model = new MonitoringKeyword();
        //$query = $model->where('id', 9)->first();

        //$store = (new PositionStore($query, false))->save();
        //dispatch((new PositionQueue($query))->onQueue('position'));


        return view('monitoring.index');
    }

    public function getProjects(Request $request)
    {
        $page = $request->input('start', 0) + 1;
        /** @var User $user */
        $user = $this->user;
        $projects = $user->monitoringProjects()->paginate($request->input('length', 1), ['*'], 'page', $page);

        $data = collect([
            'data' => (new ProjectDataTable(collect($projects->items())))->handle(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $projects->total(),
            'recordsTotal' => $projects->total(),
        ]);

        return $data;
    }

    public function getChildRowsPageByProject(int $project_id)
    {
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->where('id', $project_id)->first();

        $engines = $project->searchengines()->with('location')->get();

        $engines->transform(function($item){

            $positions = $item->positions()->get();

            $item->middle_position = __('None');
            $item->latest_position = __('None');
            $item->top_1 = 0;
            $item->top_3 = 0;
            $item->top_5 = 0;
            $item->top_10 = 0;
            $item->top_20 = 0;
            $item->top_50 = 0;
            $item->top_100 = 0;

            if($positions->isNotEmpty()){

                $item->middle_position = round($positions->sum('position') / $positions->count());
                $item->latest_position = $positions->last()->created_at;

                $last_positions = $positions->transform(function ($item){
                    if(is_null($item->position))
                        $item->position = 101;
                    return $item;
                })->sortByDesc('id')->unique('monitoring_keyword_id')->pluck('position');

                $keywords = [];
                foreach ($positions->sortByDesc('id') as $position){
                    $keywords[$position->monitoring_keyword_id][] = $position;
                }

                $last_positions_pre = [];
                foreach ($keywords as $keyword){

                    if((isset($keyword[count($keyword) - 2]))){
                        $last_positions_pre[] = $keyword[count($keyword) - 2]->position;
                    }
                }

                $item->top_1 = $this->calculatePercentByPositions($last_positions, 1) . $this->differentTopPercent($this->calculatePercentByPositions($last_positions, 1),
                    $this->calculatePercentByPositions(collect($last_positions_pre), 1));

                $item->top_3 = $this->calculatePercentByPositions($last_positions, 3) . $this->differentTopPercent($this->calculatePercentByPositions($last_positions, 3),
                    $this->calculatePercentByPositions(collect($last_positions_pre), 3));

                $item->top_5 = $this->calculatePercentByPositions($last_positions, 5) . $this->differentTopPercent($this->calculatePercentByPositions($last_positions, 5),
                    $this->calculatePercentByPositions(collect($last_positions_pre), 5));

                $item->top_10 = $this->calculatePercentByPositions($last_positions, 10) . $this->differentTopPercent($this->calculatePercentByPositions($last_positions, 10),
                    $this->calculatePercentByPositions(collect($last_positions_pre), 10));

                $item->top_20 = $this->calculatePercentByPositions($last_positions, 20) . $this->differentTopPercent($this->calculatePercentByPositions($last_positions, 20),
                    $this->calculatePercentByPositions(collect($last_positions_pre), 20));

                $item->top_50 = $this->calculatePercentByPositions($last_positions, 50) . $this->differentTopPercent($this->calculatePercentByPositions($last_positions, 50),
                    $this->calculatePercentByPositions(collect($last_positions_pre), 50));

                $item->top_100 = $this->calculatePercentByPositions($last_positions, 100) . $this->differentTopPercent($this->calculatePercentByPositions($last_positions, 100),
                    $this->calculatePercentByPositions(collect($last_positions_pre), 100));
            }

            return $item;
        });

        return view('monitoring.partials._child_rows', compact('engines'));
    }

    private function differentTopPercent($a, $b)
    {
        $total = $a - $b;

        if(!$total || !$b)
            return '';

        if($total > 0){
            $total = ' (+'. $total .')';
        }else{
            $total = ' ('. $total .')';
        }

        return $total;
    }

    private function calculatePercentByPositions(Collection $positions, int $desired)
    {
        if($positions->isEmpty())
            return 0;

        $itemsCount = $positions->count();
        $desiredCount = $positions->filter(function ($val) use ($desired){
            return $val <= $desired;
        })->count();

        $totalPercent = round(($desiredCount / $itemsCount) * 100, 2);

        return $totalPercent;
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

        $groups = $request->input('keywords');

        foreach ($groups as $group => $keywords){

            $group = $project->groups()->create([
                'type' => 'keyword',
                'name' => $group
            ]);

            foreach ($keywords['query'] as $ind => $query){
                $project->keywords()->create([
                    'monitoring_group_id' => $group->id,
                    'query' => $query,
                    'page' => $keywords['page'][$ind],
                    'target' => $keywords['target'][$ind],
                ]);
            }
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
