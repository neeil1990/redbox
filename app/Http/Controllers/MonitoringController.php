<?php

namespace App\Http\Controllers;


use App\Classes\Monitoring\Helper;
use App\Classes\Monitoring\ProjectDataTable;
use App\Classes\Position\PositionStore;
use App\Jobs\PositionQueue;
use App\MonitoringKeyword;
use App\MonitoringProject;
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

            $positions = $item->positions()->whereNotNull('position')->get();

            if($positions->isNotEmpty()){

                $this->calculateTopPercent($positions, $item);
                $item->latest_created = $positions->last()->created_at;
            }

            return $item;
        });

        return view('monitoring.partials._child_rows', compact('engines'));
    }

    private function calculateTopPercent(Collection $positions, &$model)
    {
        $percents = [
            'top_1' => 1,
            'top_3' => 3,
            'top_5' => 5,
            'top_10' => 10,
            'top_20' => 20,
            'top_50' => 50,
            'top_100' => 100,
        ];

        $last_positions = $positions->sortByDesc('id')->unique('monitoring_keyword_id');

        $pre_last_position = $positions->groupBy('monitoring_keyword_id')->transform(function($val, $i){
            $count = $val->count() - 2;

            if(isset($val[$count]))
                return $val[$count];

            return $val[$val->count() - 1];
        });

        foreach ($percents as $name => $percent){

            $last = Helper::calculateTopPercentByPositions($last_positions->pluck('position'), $percent);
            $pre_last = Helper::calculateTopPercentByPositions($pre_last_position->pluck('position'), $percent);
            $model->$name = $last . Helper::differentTopPercent($last, $pre_last);
        }

        $model->middle_position = round($last_positions->sum('position') / $last_positions->count(), 2);
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

        $navigations = $this->navigations();

        $region = $project->searchengines()->orderBy('id', 'asc')->first();

        $region->load('location');

        $position = $region->positions()->get();

        $dates = $position->unique(function($item){
            return $item->date;
        })->pluck('date')->sortByDesc(null)
            ->prepend(__('Target'))
            ->prepend(__('Group'))
            ->prepend(__('URL'))
            ->prepend(__('Query'))
            ->prepend(__(''))
            ->prepend('<input type="checkbox" id="selected-checkbox">')
            ->prepend('ID')
            ->values();

        $groups = $position->groupBy('monitoring_keyword_id')
            ->transform(function($item){

                $unique = $item->sortByDesc('id')->unique(function($item){
                    return $item->created_at->format('d.m.Y');
                });

                return $unique;
        });

        $table = [];
        $length = $dates->count();
        foreach ($groups as $id => $group){

            $table[$id] = [];
            for($i = 0; $i < $length; $i++){

                $key = MonitoringKeyword::findOrFail($id);

                switch ($i) {
                    case 0:
                        $table[$id][] = $id;
                        break;
                    case 1:
                        $table[$id][] = view('monitoring.partials.show.checkbox', ['id' => $id]);
                        break;
                    case 2:
                        $table[$id][] = view('monitoring.partials.show.btn');
                        break;
                    case 3:
                        $table[$id][] = view('monitoring.partials.show.query', ['key' => $key]);
                        break;
                    case 4:
                        $table[$id][] = view('monitoring.partials.show.url');
                        break;
                    case 5:
                        $table[$id][] = view('monitoring.partials.show.group', ['group' => $key->group]);
                        break;
                    case 6:
                        $table[$id][] = view('monitoring.partials.show.target', ['key' => $key]);
                        break;
                    default:
                        $model = $group->firstWhere('date', $dates[$i]);
                        if($model && $model->position)
                            $table[$id][] = view('monitoring.partials.show.position', ['model' => $model]);
                        else
                            $table[$id][] = '-';
                }
            }
        }

        $table = collect($table)->prepend($dates);

        return view('monitoring.show', compact('table', 'navigations', 'region'));
    }

    private function navigations()
    {
        /** @var User $user */
        $user = $this->user;

        $count = $user->monitoringProjects()->count();

        $navigations = [
            ['h3' => $count, 'p' => 'Проекты', 'icon' => 'fas fa-bezier-curve', 'href' => route('monitoring.index'), 'bg' => 'bg-info'],
            ['h3' => '150', 'p' => 'Мои конкуренты', 'icon' => 'fas fa-user-secret', 'href' => '#', 'bg' => 'bg-success'],
            ['h3' => '150', 'p' => 'Анализ ТОП-100', 'icon' => 'fas fa-chart-pie', 'href' => '#', 'bg' => 'bg-warning'],
            ['h3' => '150', 'p' => 'План продвижения', 'icon' => 'far fa-check-square', 'href' => '#', 'bg' => 'bg-danger'],
            ['h3' => '150', 'p' => 'Аудит сайта', 'icon' => 'fas fa-tasks', 'href' => '#', 'bg' => 'bg-info'],
            ['h3' => '150', 'p' => 'Отслеживание ссылок', 'icon' => 'fas fa-link', 'href' => route('backlink'), 'bg' => 'bg-purple-light'],
        ];

        return $navigations;
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
