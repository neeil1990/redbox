<?php

namespace App\Http\Controllers;

use App\Classes\Monitoring\Helper;
use App\Classes\Monitoring\ProjectDataTableUpdateDB;
use App\Jobs\AutoUpdatePositionQueue;
use App\Jobs\PositionQueue;
use App\MonitoringColumn;
use App\MonitoringDataTableColumnsProject;
use App\MonitoringKeyword;
use App\MonitoringOccurrence;
use App\MonitoringPosition;
use App\MonitoringProjectColumnsSetting;
use App\MonitoringProjectSettings;
use App\MonitoringSearchengine;
use App\MonitoringSettings;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    protected $user;
    protected $subtractionMonths = [0, 1, 3, 6, 12];

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
        //$model = new MonitoringSearchengine();
        //$searchengine = $model->find('62');

        //$model = new MonitoringKeyword();
        //$query = $model->where('id', 5600)->first();

        //(new PositionStore(false))->saveByQuery($query);
        //dispatch((new PositionQueue($query))->onQueue('position_high'));

        $lengthMenu = $this->getPaginationMenu();
        $length = (new MonitoringSettings())->getValue('pagination_project');

        return view('monitoring.index', compact('lengthMenu', 'length'));
    }

    public function parsePositionsInProject(Request $request)
    {
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->find($request->input('projectId'));
        if(!$project)
            return collect(['status' => false]);

        $engines = $project->searchengines()->whereIn('id', $request->input('regions'))->get();

        foreach ($engines as $engine){
            foreach ($project->keywords as $query)
                dispatch((new AutoUpdatePositionQueue($query, $engine))->onQueue('position_high'));
        }

        return collect(['status' => true]);
    }

    public function parsePositionsInProjectKeys(Request $request)
    {
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->where('id', $request->input('projectId'))->first();
        $keywords = $project->keywords()->whereIn('id', $request->input('keys'))->get();
        $engine = $project->searchengines()->find($request->input('region'));

        foreach ($keywords as $keyword)
            dispatch((new AutoUpdatePositionQueue($keyword, $engine))->onQueue('position_high'));

        return collect([
            'status' => true
        ]);
    }

    public function parsePositionsAllProject()
    {
        /** @var User $user */
        $user = $this->user;
        $projects = $user->monitoringProjects()->get();

        foreach ($projects as $project)
            $this->parsePositionsOfKeywordsByProjectQueue($project->id);

        return collect([
            'status' => true
        ]);
    }

    public function parsePositionsOfKeywordsByProjectQueue(int $projectId): void
    {
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->where('id', $projectId)->first();
        $project->load('keywords');

        foreach ($project->keywords as $keyword)
            dispatch((new PositionQueue($keyword))->onQueue('position_low'));
    }

    public function getProjects(Request $request)
    {
        $page = $request->input('start', 0) + 1;
        /** @var User $user */
        $user = $this->user;

        $dataTable = MonitoringDataTableColumnsProject::whereIn('monitoring_project_id', $user->monitoringProjects()->get('id')->pluck('id'));
        if(!$dataTable->count())
            $this->updateDataTableProjects();

        $model = $user->monitoringProjectsWithDataTable();

        $search = $request->input('search');
        if($search = $search['value'])
            $model = $model->where('name', 'like', $search . '%');

        if($order = Arr::first($request->input('order'))){
            $columns = $request->input('columns');
            $model->orderBy($columns[$order['column']]['name'], $order['dir']);
        }

        $projects = $this->loadSearchEnginesToProjects($model->paginate($request->input('length', 1), ['*'], 'page', $page));

        $lastUpdated = $dataTable->orderBy('updated_at', 'desc')->first();

        $data = collect([
            'data' => collect($projects->items()),
            'updatedDate' => ($lastUpdated && $lastUpdated->updated_at) ? $lastUpdated->updated_at->format('d.m.Y H:i') : null,
            'draw' => $request->input('draw'),
            'recordsFiltered' => $projects->total(),
            'recordsTotal' => $projects->total(),
        ]);

        return $data;
    }

    protected function loadSearchEnginesToProjects($projects)
    {
        $projects->transform(function($item){
            $item->load(['searchengines' => function ($query) {
                $query->groupBy('engine');
            }]);

            $item->engines = $item->searchengines->pluck('engine')->map(function ($item){
                return '<span class="badge badge-light"><i class="fab fa-'. $item .' fa-sm"></i></span>';
            })->implode(' ');

            return $item;
        });

        return $projects;
    }

    public function updateDataTableProjects()
    {
        /** @var User $user */
        $user = $this->user;
        $projects = $user->monitoringProjects()->get();
        (new ProjectDataTableUpdateDB($projects))->save();

        return response(200);
    }

    public function getChildRowsPageByProject(int $project_id, $group_id = null)
    {
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->find($project_id);
        $engines = $project->searchengines()->with('location')->get();
        $section = $project->groups()->find($group_id);

        $groups = collect([]);
        foreach ($engines as $engine){
            $engine->data = collect([]);
            $positions = $engine->positions()->whereNotNull('position');

            if($section)
                $positions->whereIn('monitoring_keyword_id', $section->keywords->pluck('id'));

            $positions = $positions->get();

            if($positions->isNotEmpty()){
                foreach ($this->subtractionMonths as $month){
                    if($grouped = $this->groupPositionsByMonth($positions, $month)){
                        $engine->data->push($this->calculateTopPercent($grouped, $engine));
                    }
                }
            }
            $groups->push($engine);
        }

        return view('monitoring.partials._child_rows', compact('groups'));
    }

    private function calculateTopPercent(Collection $positions, $model)
    {
        $engine = clone $model;

        $percents = [
            'top_1' => 1,
            'top_3' => 3,
            'top_5' => 5,
            'top_10' => 10,
            'top_20' => 20,
            'top_50' => 50,
            'top_100' => 100,
        ];

        $pos = $this->getLastCoupleOfPositions($positions);

        foreach ($percents as $name => $percent){
            $first = Helper::calculateTopPercentByPositions($pos->pluck('first.position'), $percent);
            $last = Helper::calculateTopPercentByPositions($pos->pluck('last.position'), $percent);
            $engine->$name = $first . Helper::differentTopPercent($first, $last);
        }
        $engine->middle_position = round($pos->pluck('first')->sum('position') / $pos->pluck('first')->count(), 2);
        $engine->latest_created = $pos->pluck('first')->last()->created_at;

        return $engine;
    }

    public function getLastCoupleOfPositions(Collection $positions)
    {
        $positions = $positions->groupBy('monitoring_keyword_id')->transform(function($pos){
            $p = $pos->sortByDesc('created_at')->values();

            if($p->count() > 1)
                return collect(['first' => $p[0], 'last' => $p[1]]);
            elseif ($p->count())
                return collect(['first' => $p[0], 'last' => []]);
            else
                return collect(['first' => [], 'last' => []]);
        });

        return $positions;
    }

    public function groupPositionsByMonth(Collection $positions, int $subMonth = null)
    {
        $format = 'Y-m';

        $grouped = $positions->groupBy(function ($item) use ($format) {
            return $item->created_at->format($format);
        })->sortByDesc(function($i, $k){ return Carbon::parse($k)->timestamp; });

        if($subMonth === null)
            return $grouped;

        $carbon = Carbon::now()->subMonths($subMonth)->format($format);
        if($grouped->has($carbon))
            return $grouped[$carbon];

        return null;
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $navigations = $this->navigations();

        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->where('id', $id)->first();

        $length = $this->getLength($project->id);
        $lengthMenu = $this->getPaginationMenu();

        return view('monitoring.show', compact('navigations', 'project', 'length', 'lengthMenu'));
    }

    public function getPaginationMenu()
    {
        $lengthMenu = '[10,20,30,50]';

        if($global = (new MonitoringSettings())->getValue('pagination_items'))
            $lengthMenu = '['. $global .']';

        return $lengthMenu;
    }

    public function getLength(int $projectId)
    {
        $lengthDefault = 100;

        if($global = (new MonitoringSettings())->getValue('pagination_query'))
            $lengthDefault = $global;

        if($length = $this->getSetting($projectId, 'length'))
            $lengthDefault = $length->value;

        return $lengthDefault;
    }

    public function getColumnSettings()
    {
        $user = $this->user;
        return MonitoringColumn::where(['user_id' => $user['id']])->get();
    }

    public function setColumnSettings(Request $request)
    {
        $user = $this->user;
        MonitoringColumn::updateOrCreate(
            ['user_id' => $user['id'], 'column' => $request->input('column')],
            ['state' => $request->input('state')]
        );
    }

    public function setColumnSettingsForProject(Request $request)
    {
        MonitoringProjectColumnsSetting::updateOrCreate(
            ['monitoring_project_id' => $request->input('monitoring_project_id'), 'name' => $request->input('name')],
            ['state' => $request->input('state')]
        );
    }

    public function getColumnSettingsForProject(Request $request)
    {
        return MonitoringProjectColumnsSetting::where(['monitoring_project_id' => $request->input('monitoring_project_id')])->get();
    }

    public function getSetting(int $idProject, string $name)
    {
        return MonitoringProjectSettings::where(['monitoring_project_id' => $idProject, 'name' => $name])->first();
    }

    public function setSetting(int $idProject, string $name, string $value)
    {
        MonitoringProjectSettings::updateOrCreate(
            ['monitoring_project_id' => $idProject, 'name' => $name],
            ['value' => $value]
        );
    }

    protected function getKeywordIdsWithNotValidateUrl(int $projectId, int $regionId)
    {
        $lastDateUrlPosition = DB::table('monitoring_positions')
            ->select('monitoring_keyword_id', 'monitoring_searchengine_id', DB::raw('MAX(created_at) created_max'))
            ->whereNotNull('url')
            ->where('monitoring_searchengine_id', $regionId)
            ->groupBy('monitoring_keyword_id');

        $lastUrlPosition = DB::table('monitoring_positions')
            ->joinSub($lastDateUrlPosition, 'latest_url', function($join){
                $join->on('monitoring_positions.monitoring_keyword_id', '=', 'latest_url.monitoring_keyword_id')
                    ->on('monitoring_positions.created_at', '=', 'latest_url.created_max');
            })
            ->join('monitoring_keywords', function ($join) {
                $join->on('monitoring_positions.monitoring_keyword_id', '=', 'monitoring_keywords.id')
                    ->on('monitoring_positions.url', '!=', 'monitoring_keywords.page');
            })
            ->where('monitoring_keywords.monitoring_project_id', $projectId)
            ->where('monitoring_positions.monitoring_searchengine_id', $regionId)
            ->get()
            ->pluck('id');

        return $lastUrlPosition;
    }

    public function getTableKeywords(Request $request, $id)
    {

        $mode = $request->input('mode_range');
        $regionId = $request->input('region_id');

        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->where('id', $id)->first();

        if($project->searchengines->count() > 1 && empty($regionId))
            $mode = 'main';
        else{
            if(empty($regionId))
                $regionId = $project->searchengines->first()->id;
        }

        $keywords = $project->keywords();

        $region = $this->getRegion($project, $regionId);
        if($mode === 'main')
            $region = $this->getRegions($project);

        $this->filter($project, $keywords, $region, $request);

        $this->orderTableKeywords($keywords, $request->input('order'));

        $page = ($request->input('start') / $request->input('length')) + 1;
        $keywords = $keywords->paginate($request->input('length', 1), ['*'], 'page', $page);

        $this->setSetting($project->id, 'length', $request->input('length'));

        $dates = null;
        if($request->input('dates_range', null)){
            $dates = explode(' - ', $request->input('dates_range'), 2);
        }

        $keywords->load('group');

        $keywords->load(['positions' => function($query) use ($region, $dates, $mode){

            if(isset($region->id))
                $query->where('monitoring_searchengine_id', $region->id);

            if($mode === "datesFind")
                $query->dateFind($dates);
            else
                $query->dateRange($dates);
        }]);

        if(isset($region->id)){
            $keywords = $this->setUrlsInKeywords($keywords, $region->id);
        }

        $keywords = $this->setOccurrenceInKeywords($keywords, $region);

        $columns = $this->getMainColumns($keywords);

        $this->getLastPositions($keywords,$columns, $mode, $region, $dates);

        $table = $this->generateDataTable($keywords, $columns, $mode);

        $data = collect([
            'region' => $region,
            'columns' => $columns,
            'data' => collect($table)->values(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $keywords->total(),
            'recordsTotal' => $keywords->total(),
        ]);

        return $data;
    }

    private function setOccurrenceInKeywords($query, $region)
    {
        $collection = collect([]);
        if($region instanceof MonitoringSearchengine)
            $collection->push($region);
        else
            $collection = $region;

        $query->transform(function($item) use ($collection){
            foreach($collection as $region){
                $occurrence = MonitoringOccurrence::where(['monitoring_keyword_id' => $item->id, 'monitoring_searchengine_id' => $region['id']])->first();
                if($occurrence){
                    $item->base += $occurrence->base;
                    $item->phrasal += $occurrence->phrasal;
                    $item->exact += $occurrence->exact;

                    $item->occurrenceCreateAt = $occurrence->updated_at;
                }
            }

            return $item;
        });

        return $query;
    }

    private function orderTableKeywords(&$keywords, $order = null)
    {
        $dir = 'asc';

        if($order && is_array($order)){
            $order = collect($order)->collapse();

            if($order->has('dir') && $order['dir'] != $dir)
                $dir = $order['dir'];
        }

        $keywords->orderBy('query', $dir);
    }

    private function updateKeywordsDynamic($keywords, $region, $request)
    {
        $mode = $request->input('mode_range', 'range');
        $dates = null;
        if($request->input('dates_range', null)){
            $dates = explode(' - ', $request->input('dates_range'), 2);
        }

        $keywords = $keywords->get();

        $keywords->load(['positions' => function($query) use ($region, $dates, $mode){

            $query->where('monitoring_searchengine_id', $region->id);

            if($mode === "datesFind")
                $query->dateFind($dates);
            else
                $query->dateRange($dates);
        }]);

        $columns = $this->getMainColumns();
        $this->getLastPositions($keywords,$columns, $mode, $region, $dates);

        foreach ($keywords as $keyword){
            $dynamics = 0;
            $model = $keyword->positions_view;

            if($model && $model->count() > 1)
                $dynamics = ($model->last()->position - $model->first()->position);

            MonitoringKeyword::where('id', $keyword->id)->update(['dynamic' => $dynamics]);
        }
    }

    private function getLastPositions(&$keywords, &$columns, $mode, $region, $dates)
    {
        $dateCollection = collect([]);
        foreach($keywords as &$keyword){

            $grouped = $keyword->positions->groupBy(function($item){
                return $item->created_at->format('d.m.Y');
            })->sortByDesc(function($i, $k){ return Carbon::parse($k)->timestamp; });

            $grouped->transform(function ($item) {
                return $item->sortByDesc(function($i){ return $i->created_at->timestamp; })->values()->first();
            });

            foreach($grouped->keys() as $date)
                if(!$dateCollection->contains($date))
                    $dateCollection->push($date);

            $keyword->positions_data_table = $grouped;
        }

        $columnCollection = collect([]);
        foreach ($dateCollection->sortByDesc(function($i){ return Carbon::parse($i)->timestamp; }) as $col_idx => $col_date)
            $columnCollection->put('col_' . $col_idx, $col_date);

        switch ($mode){
            case "dates":
                $columns = $columns->merge(collect([
                    'col_0' => __('First of find'),
                    'col_1' => __('Last of find'),
                ]));

                $keywords->transform(function($item){
                    $item->positions_view = collect([
                        'col_0' => $item->positions_data_table->first(),
                        'col_1' => $item->positions_data_table->last(),
                    ]);

                    return $item;
                });
                break;
            case "randWeek":
            case "randMonth":
                $keywords->transform(function($item) use ($mode){
                    $positionsRange = collect([]);
                    foreach ($item->positions_data_table as $p){
                        if($mode === "randWeek")
                            $positionsRange->put($p->created_at->week(), $p);
                        else
                            $positionsRange->put($p->created_at->month, $p);
                    }

                    $item->positions_view = $positionsRange;

                    return $item;
                });

                $getDateForColumns = collect([]);
                foreach ($keywords as $keyword)
                    $getDateForColumns = $getDateForColumns->merge($keyword->positions_view->pluck('created_at'));

                $getDateForColumns = $getDateForColumns->sortByDesc(null)->unique(function($item){
                    return $item->format('d.m.Y');
                });

                $dateOfColumns = collect([]);
                foreach ($getDateForColumns as $i => $m)
                    $dateOfColumns->put('col_' . $i, $m->format('d.m.Y'));

                $columns = $columns->merge($dateOfColumns);

                foreach ($keywords as $keyword){
                    $lastPosition = collect([]);
                    foreach ($dateOfColumns as $col => $name){
                        if($keyword->positions_data_table->has($name))
                            $lastPosition->put($col, $keyword->positions_data_table[$name]);
                    }
                    $keyword->positions_view = $lastPosition;
                }
                break;
            case "main":
                $mainColumns = collect([]);
                $keywords->transform(function($item) use ($region, $mainColumns){

                    $lastPosition = collect([]);
                    foreach ($region as $reg){

                        $model = $item->positions()->where('monitoring_searchengine_id', $reg->id)->latest()->get();
                        $col = 'engine_' . $reg->lr;

                        if($model->isNotEmpty()){
                            $monitoringPosition = $model->first();

                            if($monitoringPosition->id != $model->last()->id)
                                $monitoringPosition->diffPosition = ($model->last()->position - $monitoringPosition->position);
                            else
                                $monitoringPosition->diffPosition = null;

                            $lastPosition->put($col, $model->first());
                        }

                        $city = stristr($reg->location->name, ',', true);
                        $icon = '<i class="fab d-block fa-'. $reg->engine .' fa-sm"></i>';

                        $mainColumns->put($col, implode(' ', [$icon, $city]));
                    }

                    $item->positions_view = $lastPosition;

                    return $item;
                });

                $columns = $columns->merge($mainColumns);
                $columns->forget('url');

                break;
            default;
                $columns = $columns->merge($columnCollection);
                $keywords->transform(function($item) use ($columnCollection){

                    $positions = collect([]);
                    foreach ($columnCollection as $col => $name)
                        if($item->positions_data_table->has($name))
                            $positions->put($col, $item->positions_data_table[$name]);

                    $this->diffPositionExtension($positions);

                    $item->positions_view = $positions;

                    return $item;
                });
            }
    }

    private function diffPositionExtension(&$positions)
    {
        if($positions->isEmpty())
            return false;

        $pre = 0;

        foreach($positions->reverse() as $p){
            if($pre > 0)
                $p->diffPosition = ($pre - $p->position);
            else
                $p->diffPosition = null;

            $pre = $p->position;
        }
    }

    private function generateDataTable($keywords, $columns, $mode)
    {
        $table = [];
        foreach ($keywords as $keyword){

            $id = $keyword->id;
            $table[$id] = $this->generateRowDataTable($columns, $keyword, $mode);
        }

        return $table;
    }

    private function generateRowDataTable($columns, $keyword, $mode)
    {
        $row = collect([]);
        $collectionPositions = $keyword->positions_view;

        foreach ($columns as $i => $v){

            switch ($i) {
                case 'id':
                    $row->put('id', $keyword->id);
                    break;
                case 'checkbox':
                    $row->put('checkbox', view('monitoring.partials.show.checkbox', ['id' => $keyword->id])->render());
                    break;
                case 'btn':
                    $row->put('btn', view('monitoring.partials.show.btn', ['key' => $keyword])->render());
                    break;
                case 'query':
                    $row->put('query', view('monitoring.partials.show.query', ['key' => $keyword])->render());
                    break;
                case 'url':
                    if(isset($keyword->urls)){
                        $urls = $keyword->urls;
                        $textClass = 'text-bold';
                        if($keyword->page && $urls->count()){
                            $lastUrl = $urls->first();
                            if($lastUrl->url != $keyword->page)
                                $textClass = 'text-danger';
                            else
                                $textClass = 'text-success';
                        }

                        $row->put('url', view('monitoring.partials.show.url', ['textClass' => $textClass, 'urls' => $urls])->render());
                    }else
                        $row->put($i, '-');
                    break;
                case 'group':
                    $row->put('group', view('monitoring.partials.show.group', ['group' => $keyword->group])->render());
                    break;
                case 'target':
                    $row->put('target', view('monitoring.partials.show.target', ['key' => $keyword])->render());
                    break;
                case 'dynamics':
                    $dynamics = 0;
                    if($collectionPositions && $collectionPositions->count() > 1)
                        $dynamics = ($collectionPositions->last()->position - $collectionPositions->first()->position);

                    $row->put('dynamics', view('monitoring.partials.show.dynamics', ['dynamics' => $dynamics])->render());
                    break;
                case 'base':
                    if(isset($keyword->base))
                        $row->put('base', $keyword->base);
                    else
                        $row->put('base', '-');
                    break;
                case 'phrasal':
                    if(isset($keyword->phrasal))
                        $row->put('phrasal', $keyword->phrasal);
                    else
                        $row->put('phrasal', '-');
                    break;
                case 'exact':
                    if(isset($keyword->exact))
                        $row->put('exact', $keyword->exact);
                    else
                        $row->put('exact', '-');
                    break;
                default:

                    if($mode === "dates" || $mode === "main"){
                        if(isset($collectionPositions[$i]))
                            $row->put($i, view('monitoring.partials.show.position_with_date', ['model' => $collectionPositions[$i]])->render());
                        else
                            $row->put($i, '-');

                    }else{
                        if(isset($collectionPositions[$i])) {
                            $row->put($i, view('monitoring.partials.show.position', ['model' => $collectionPositions[$i]])->render());
                        }else
                            $row->put($i, '-');
                    }
            }
        }

        return $row;
    }

    private function setUrlsInKeywords($keywords, $regionId)
    {
        $ids = $keywords->pluck('id');

        $model = MonitoringPosition::select('monitoring_keyword_id', 'url', 'created_at')
            ->where('monitoring_searchengine_id', $regionId)
            ->whereNotNull('url')
            ->whereIn('monitoring_keyword_id', $ids)->orderBy('created_at', 'desc')->get();

        $urls = $model->groupBy('monitoring_keyword_id');

        $keywords->transform(function($item) use ($urls){

            $item->urls = collect([]);

            if(isset($urls[$item->id]))
                $item->urls = $urls[$item->id]->unique('url');

            return $item;
        });

        return $keywords;
    }

    public function getPositionsForCalendars(Request $request)
    {
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->where('id', $request->input('projectId'))->first();
        $region = $project->searchengines();

        if($request->input('regionId'))
            $region->where('id', $request->input('regionId'));

        $region = $region->orderBy('id', 'asc')->first();
        $keywordsId = $project->keywords->pluck('id');

        $dates = collect($request->input('dates'))->pluck('date');

        $positions = MonitoringPosition::select(DB::raw('*, DATE(created_at) as dateOnly'))
            ->where('monitoring_searchengine_id', $region->id)
            ->whereIn('monitoring_keyword_id', $keywordsId)
            ->whereIn(DB::raw('DATE(created_at)'), $dates)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        return $positions;
    }

    private function getMainColumns($keywords = null)
    {
        $text = '';

        if($keywords){
            $query = $keywords->first();
            if(isset($query['occurrenceCreateAt']))
                $text = $query['occurrenceCreateAt']->format('d.m.Y');
        }

        $columns = collect([
            'checkbox' => '',
            'btn' => '',
            'query' => view('monitoring.partials.show.header.query')->render(),
            'url' => __('URL'),
            'group' => __('Group'),
            'target' => __('Target'),
            'dynamics' => __('Dynamics'),
            'base' => view('monitoring.partials.show.header.yw', ['ext' => '', 'date' => $text])->render(),
            'phrasal' => view('monitoring.partials.show.header.yw', ['ext' => '"[]"', 'date' => $text])->render(),
            'exact' => view('monitoring.partials.show.header.yw', ['ext' => '"[!]"', 'date' => $text])->render(),
        ]);

        return $columns;
    }

    protected function filter($project, &$model, $region, Request $request)
    {
        $columns = $request->input('columns', []);

        foreach ($columns as $column){

            switch ($column['data']) {
                case 'query':
                    if($column['search']['value'])
                        $model->where('query', 'like', '%'.$column['search']['value'].'%');
                    break;
                case 'group':
                    if($column['search']['value'])
                        $model->where('monitoring_group_id', '=', $column['search']['value']);
                    break;
                case 'url':
                    if($column['search']['value'])
                        $model->whereIn('id', $this->getKeywordIdsWithNotValidateUrl($project->id, $region->id));
                    break;
                case 'dynamics':
                    if($column['search']['value']){
                        $this->updateKeywordsDynamic($model, $region, $request);
                        if($column['search']['value'] == 'positive')
                            $model->where('dynamic', '>', 0);
                        elseif ($column['search']['value'] == 'negative')
                            $model->where('dynamic', '<', 0);
                    }
                    break;
            }
        }

        return $columns;
    }

    private function navigations()
    {
        /** @var User $user */
        $user = $this->user;

        $count = $user->monitoringProjects()->count();

        $navigations = [
            ['h3' => $count, 'p' => 'Проекты', 'small' => '', 'icon' => 'fas fa-bezier-curve', 'href' => route('monitoring.index'), 'bg' => 'bg-info'],
            ['h3' => '150', 'p' => 'Мои конкуренты', 'small' => 'В разработке', 'icon' => 'fas fa-user-secret', 'href' => '#', 'bg' => 'bg-success'],
            ['h3' => '150', 'p' => 'Анализ ТОП-100', 'small' => 'В разработке', 'icon' => 'fas fa-chart-pie', 'href' => '#', 'bg' => 'bg-warning'],
            ['h3' => '150', 'p' => 'План продвижения', 'small' => 'В разработке', 'icon' => 'far fa-check-square', 'href' => '#', 'bg' => 'bg-danger'],
            ['h3' => '150', 'p' => 'Аудит сайта', 'small' => 'В разработке', 'icon' => 'fas fa-tasks', 'href' => '#', 'bg' => 'bg-info'],
            ['h3' => '150', 'p' => 'Отслеживание ссылок', 'small' => '', 'icon' => 'fas fa-link', 'href' => route('backlink'), 'bg' => 'bg-purple-light'],
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

    /**
     * @param $id
     * @param $project
     * @return mixed
     */
    protected function getRegion($project, $regionId = null)
    {
        $searchEngines = $project->searchengines();

        if ($regionId)
            $searchEngines = $searchEngines->where('id', $regionId);

        $region = $searchEngines->orderBy('id', 'asc')->first();

        $region->load('location');

        return $region;
    }

    /**
     * @param $id
     * @param $project
     * @return mixed
     */
    protected function getRegions($project)
    {
        $searchEngines = $project->searchengines();

        $region = $searchEngines->orderBy('id', 'asc')->get();

        $region->load('location');

        return $region;
    }
}
