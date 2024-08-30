<?php

namespace App\Http\Controllers;

use App\Classes\Monitoring\Mastered;
use App\Classes\Position\PositionStore;
use App\Jobs\PositionQueue;
use App\Location;
use App\MonitoringKeyword;
use App\MonitoringKeywordPrice;
use App\MonitoringOccurrence;
use App\MonitoringPosition;
use App\MonitoringProject;
use App\MonitoringProjectSettings;
use App\MonitoringSearchengine;
use App\User;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MonitoringKeywordsController extends Controller
{
    protected $user;
    protected $project;
    protected $projectID;
    protected $queries;
    protected $regions;
    protected $columns;
    protected $mode = "range";
    protected $total = 0;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });

        $this->columns = $this->getColumns();
    }

    public function setMode(string $mode = null): void
    {
        if(strlen($mode) > 1)
            $this->mode = $mode;
    }

    public function setColumns(Collection $collection)
    {
        $this->columns = $this->columns->merge($collection);
    }

    public function setProjectID($id)
    {
        $this->projectID = $id;

        return $this;
    }

    public function getProjectID()
    {
        if(!$this->projectID)
            throw new \Exception('Project ID does not exist, insert project ID.');

        return $this->projectID;
    }

    private function init()
    {
        $id = $this->getProjectID();
        $this->project = $this->user->monitoringProjects()->find($id);
        $this->regions = $this->project->searchengines()->with('location')->orderBy('id', 'asc')->get();
        $this->queries = $this->project->keywords();
    }

    public function showDataTable(Request $request, $id)
    {
        $this->setProjectID($id);
        $request = collect($request->all());

        return $this->dataPrepare($request)->generateDataTable($request->get('draw', 0));
    }

    public function dataPrepare(Collection $collection)
    {
        $this->init();
        $regionID = $collection->get('region_id');
        $order = $collection->get('order');
        $start = $collection->get('start');
        $length = $collection->get('length');
        $filteredColumns = $collection->get('columns', []);
        $datesRange = $collection->get('dates_range');

        $this->setMode($collection->get('mode_range'));

        if($regionID)
            $this->regions = $this->regions->where('id', $regionID);

        $this->filter($filteredColumns)->order($order);

        $page = ($length) ? ($start / $length) + 1 : false;
        if($page){
            $this->queries = $this->queries->paginate($length, ['*'], 'page', $page);
            $this->total = $this->queries->total();
        }else{
            $this->queries = $this->queries->get();
            $this->total = $this->queries->count();
        }

        if($length > 1)
            $this->setSetting($this->getProjectID(), 'length', $length);

        $dates = null;
        if (strlen($datesRange) > 1)
            $dates = explode(' - ', $datesRange, 2);

        $this->loadPositions($dates);

        $this->setOccurrence();

        if($this->isMainView()){
            $this->mainView();
            $this->columns->forget(['url', 'dynamics']);
        }else{
            $this->setUrls();
            $this->getLatestPositions()->updateDynamics();
        }

        return $this;
    }

    private function isMainView()
    {
        return ($this->regions->count() > 1);
    }

    private function mainView()
    {
        $mainColumns = collect([]);
        $regions = $this->regions;
        $this->queries->transform(function ($item) use ($regions, $mainColumns) {

            $lastPosition = collect([]);
            foreach ($regions as $reg) {

                $model = $item->positions()->where('monitoring_searchengine_id', $reg->id)->latest()->get();
                $col = 'engine_' . $reg->lr;

                if ($model->isNotEmpty()) {
                    $monitoringPosition = $model->first();

                    if ($monitoringPosition->id != $model->last()->id)
                        $monitoringPosition->diffPosition = ($model->last()->position - $monitoringPosition->position);
                    else
                        $monitoringPosition->diffPosition = null;

                    $lastPosition->put($col, $model->first());
                }

                $city = stristr($reg->location->name, ',', true);
                $icon = '<i class="fab d-block fa-' . $reg->engine . ' fa-sm"></i>';

                $mainColumns->put($col, implode(' ', [$icon, $city]));
            }

            $item->positions_view = $lastPosition;

            return $item;
        });

        $this->setColumns($mainColumns);
    }

    protected function generateDataTable($draw = 0)
    {
        $table = [];
        foreach ($this->queries as $keyword) {
            $id = $keyword->id;
            $table[$id] = $this->generateRowDataTable($keyword);
        }

        return collect([
            'region' => $this->regions->values(),
            'columns' => $this->columns,
            'data' => collect($table)->values(),
            'draw' => $draw,
            'recordsFiltered' => $this->total,
            'recordsTotal' => $this->total,
        ]);
    }

    private function generateRowDataTable($keyword)
    {
        $row = collect([]);
        $collectionPositions = $keyword->positions_view;

        if($this->mode == 'finance')
            $mastered = new Mastered($collectionPositions);

        $columns = $this->columns;

        foreach ($columns as $i => $v) {

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
                    if (isset($keyword->urls)) {
                        $urls = $keyword->urls;
                        $textClass = 'text-bold';
                        if ($keyword->page && $urls->count()) {
                            $lastUrl = $urls->first();
                            if ($lastUrl->url != $keyword->page)
                                $textClass = 'text-danger';
                            else
                                $textClass = 'text-success';
                        }

                        $row->put('url', view('monitoring.partials.show.url', ['textClass' => $textClass, 'urls' => $urls])->render());
                    } else
                        $row->put($i, '-');
                    break;
                case 'group':
                    $row->put('group', view('monitoring.partials.show.group', ['group' => $keyword->group])->render());
                    break;
                case 'target_url':
                    $row->put('target_url', $keyword->page);
                    break;
                case 'target':
                    $row->put('target', view('monitoring.partials.show.target', ['key' => $keyword])->render());
                    break;
                case 'dynamics':
                    $dynamics = 0;
                    if ($collectionPositions && $collectionPositions->count() > 1)
                        $dynamics = ($collectionPositions->last()->position - $collectionPositions->first()->position);

                    $row->put('dynamics', view('monitoring.partials.show.dynamics', ['dynamics' => $dynamics])->render());
                    break;
                case 'base':
                    if (isset($keyword->base))
                        $row->put('base', $keyword->base);
                    else
                        $row->put('base', '-');
                    break;
                case 'phrasal':
                    if (isset($keyword->phrasal))
                        $row->put('phrasal', $keyword->phrasal);
                    else
                        $row->put('phrasal', '-');
                    break;
                case 'exact':
                    if (isset($keyword->exact))
                        $row->put('exact', $keyword->exact);
                    else
                        $row->put('exact', '-');
                    break;
                case 'price_top_1':
                    $row->put('price_top_1', ($keyword->price) ? $keyword->price['top1'] : 0);
                    break;
                case 'price_top_3':
                    $row->put('price_top_3', ($keyword->price) ? $keyword->price['top3'] : 0);
                    break;
                case 'price_top_5':
                    $row->put('price_top_5', ($keyword->price) ? $keyword->price['top5'] : 0);
                    break;
                case 'price_top_10':
                    $row->put('price_top_10', ($keyword->price) ? $keyword->price['top10'] : 0);
                    break;
                case 'price_top_20':
                    $row->put('price_top_20', ($keyword->price) ? $keyword->price['top20'] : 0);
                    break;
                case 'price_top_50':
                    $row->put('price_top_50', ($keyword->price) ? $keyword->price['top50'] : 0);
                    break;
                case 'price_top_100':
                    $row->put('price_top_100', ($keyword->price) ? $keyword->price['top100'] : 0);
                    break;
                case 'days_top_1':
                    $top = $mastered->top1();
                    $row->put('days_top_1', $top['count']);
                    break;
                case 'days_top_3':
                    $top = $mastered->top3();
                    $row->put('days_top_3', $top['count']);
                    break;
                case 'days_top_5':
                    $top = $mastered->top5();
                    $row->put('days_top_5', $top['count']);
                    break;
                case 'days_top_10':
                    $top = $mastered->top10();
                    $row->put('days_top_10', $top['count']);
                    break;
                case 'days_top_20':
                    $top = $mastered->top20();
                    $row->put('days_top_20', $top['count']);
                    break;
                case 'days_top_50':
                    $top = $mastered->top50();
                    $row->put('days_top_50', $top['count']);
                    break;
                case 'days_top_100':
                    $top = $mastered->top100();
                    $row->put('days_top_100', $top['count']);
                    break;
                case 'mastered':
                    $row->put('mastered', $mastered->total());
                    break;
                default:
                    $mode = $this->mode;
                    if ($mode === "dates" || $mode === "main") {
                        if (isset($collectionPositions[$i]))
                            $row->put($i, view('monitoring.partials.show.position_with_date', ['model' => $collectionPositions[$i]])->render());
                        else
                            $row->put($i, '-');

                    } else {
                        if (isset($collectionPositions[$i])) {
                            $row->put($i, view('monitoring.partials.show.position', ['model' => $collectionPositions[$i]])->render());
                        } else
                            $row->put($i, '-');
                    }
            }
        }

        return $row;
    }

    private function getLatestPositions()
    {
        $dateCollection = collect([]);
        foreach ($this->queries as &$keyword) {

            $grouped = $keyword->positions->groupBy(function ($item) {
                return $item->created_at->format('d.m.Y');
            })->sortByDesc(function ($i, $k) {
                return Carbon::parse($k)->timestamp;
            });

            $grouped->transform(function ($item) {
                return $item->sortByDesc(function ($i) {
                    return $i->created_at->timestamp;
                })->values()->first();
            });

            foreach ($grouped->keys() as $date)
                if (!$dateCollection->contains($date))
                    $dateCollection->push($date);

            $keyword->positions_data_table = $grouped;
        }

        $columnCollection = collect([]);
        foreach ($dateCollection->sortByDesc(function ($i) {
            return Carbon::parse($i)->timestamp;
        }) as $col_idx => $col_date)
            $columnCollection->put('col_' . $col_idx, $col_date);

        switch ($this->mode) {
            case "dates":
                $this->setColumns(collect([
                    'col_0' => __('First of find'),
                    'col_1' => __('Last of find'),
                ]));

                $this->queries->transform(function ($item) {
                    $item->positions_view = collect([
                        'col_0' => $item->positions_data_table->first(),
                        'col_1' => $item->positions_data_table->last(),
                    ]);

                    return $item;
                });
                break;
            case "randWeek":
            case "randMonth":
            $this->queries->transform(function ($item) {
                    $positionsRange = collect([]);
                    foreach ($item->positions_data_table as $p) {
                        if ($this->mode === "randWeek")
                            $positionsRange->put($p->created_at->week(), $p);
                        else
                            $positionsRange->put($p->created_at->month, $p);
                    }

                    $item->positions_view = $positionsRange;

                    return $item;
                });

                $getDateForColumns = collect([]);
                foreach ($this->queries as $keyword)
                    $getDateForColumns = $getDateForColumns->merge($keyword->positions_view->pluck('created_at'));

                $getDateForColumns = $getDateForColumns->sortByDesc(null)->unique(function ($item) {
                    return $item->format('d.m.Y');
                });

                $dateOfColumns = collect([]);
                foreach ($getDateForColumns as $i => $m)
                    $dateOfColumns->put('col_' . $i, $m->format('d.m.Y'));

                $this->setColumns($dateOfColumns);

                foreach ($this->queries as $keyword) {
                    $lastPosition = collect([]);
                    foreach ($dateOfColumns as $col => $name) {
                        if ($keyword->positions_data_table->has($name))
                            $lastPosition->put($col, $keyword->positions_data_table[$name]);
                    }
                    $keyword->positions_view = $lastPosition;
                }
                break;
            case "finance":
                $this->financeExtension($columnCollection);
                break;
            default;
                $this->setColumns($columnCollection);
                $this->queries->transform(function ($item) use ($columnCollection) {

                    $positions = collect([]);
                    foreach ($columnCollection as $col => $name)
                        if ($item->positions_data_table->has($name))
                            $positions->put($col, $item->positions_data_table[$name]);

                    $this->diffPositionExtension($positions);

                    $item->positions_view = $positions;

                    return $item;
                });
        }

        return $this;
    }

    private function financeExtension($columns)
    {
        $this->setColumns($columns);

        $this->queries->transform(function ($item) use ($columns) {

            $positions = collect([]);
            foreach ($columns as $col => $name)
                if ($item->positions_data_table->has($name))
                    $positions->put($col, $item->positions_data_table[$name]);

            $this->diffPositionExtension($positions);

            $item->positions_view = $positions;

            return $item;
        });

        $fields = ['top_1', 'top_3', 'top_5', 'top_10', 'top_20', 'top_50', 'top_100'];

        $price = collect([]);
        $days = collect([]);

        foreach($fields as $field){
            $price->put('price_' . $field, __('Price') . ' ' . str_replace("_", "-", $field));
            $days->put('days_' . $field, __('Days') . ' ' . str_replace("_", "-", $field));
        }

        $this->setColumns($price);
        $this->setColumns($days);

        $this->setColumns(collect(['mastered' => __('Mastered')]));
    }

    private function diffPositionExtension(&$positions)
    {
        if ($positions->isEmpty())
            return false;

        $pre = 0;

        foreach ($positions->reverse() as $p) {
            if ($pre > 0)
                $p->diffPosition = ($pre - $p->position);
            else
                $p->diffPosition = null;

            $pre = $p->position;
        }
    }

    private function getColumns()
    {
        $columns = collect([
            'checkbox' => '',
            'btn' => '',
            'query' => view('monitoring.partials.show.header.query')->render(),
            'url' => __('URL'),
            'group' => __('Group'),
            'target_url' => __('Target URL'),
            'target' => __('Target'),
            'dynamics' => __('Dynamics'),
            'base' => view('monitoring.partials.show.header.yw', ['ext' => ''])->render(),
            'phrasal' => view('monitoring.partials.show.header.yw', ['ext' => '"[]"'])->render(),
            'exact' => view('monitoring.partials.show.header.yw', ['ext' => '"[!]"'])->render(),
        ]);

        return $columns;
    }

    private function setOccurrence()
    {
        $collection = $this->regions;
        $this->queries->transform(function ($item) use ($collection) {
            foreach ($collection as $region) {
                $occurrence = MonitoringOccurrence::where(['monitoring_keyword_id' => $item->id, 'monitoring_searchengine_id' => $region['id']])->first();
                if ($occurrence) {
                    $item->base += $occurrence->base;
                    $item->phrasal += $occurrence->phrasal;
                    $item->exact += $occurrence->exact;

                    $item->occurrenceCreateAt = $occurrence->updated_at;
                }
            }

            return $item;
        });
    }

    private function setUrls()
    {
        $ids = $this->queries->pluck('id');
        $region = $this->regions->first();

        $model = MonitoringPosition::select('monitoring_keyword_id', 'url', 'created_at')
            ->where('monitoring_searchengine_id', $region['id'])
            ->whereNotNull('url')
            ->whereIn('monitoring_keyword_id', $ids)->orderBy('created_at', 'desc')->get();

        $urls = $model->groupBy('monitoring_keyword_id');

        $this->queries->transform(function ($item) use ($urls) {

            $item->urls = collect([]);

            if (isset($urls[$item->id]))
                $item->urls = $urls[$item->id]->unique('url');

            return $item;
        });
    }

    private function loadPositions($dates)
    {
        $region = $this->regions->first();
        $this->queries->load(['positions' => function ($query) use ($region, $dates) {

            if (isset($region->id))
                $query->where('monitoring_searchengine_id', $region->id);

            if ($this->mode === "datesFind")
                $query->dateFind($dates);
            else
                $query->dateRange($dates);
        }]);
    }

    public function setSetting(int $idProject, string $name, string $value)
    {
        MonitoringProjectSettings::updateOrCreate(
            ['monitoring_project_id' => $idProject, 'name' => $name],
            ['value' => $value]
        );
    }

    private function order($by = null)
    {
        $dir = 'asc';

        if ($by && is_array($by)) {
            $order = collect($by)->collapse();

            if ($order->has('dir') && $order['dir'] != $dir)
                $dir = $order['dir'];
        }

        $this->queries->orderBy('query', $dir);

        return $this;
    }

    private function filter(array $columns)
    {
        $project = $this->project;
        $model = $this->queries;
        $region = $this->regions->first();

        foreach ($columns as $column) {

            switch ($column['data']) {
                case 'query':
                    if ($column['search']['value'])
                        $model->where('query', 'like', '%' . $column['search']['value'] . '%');
                    break;
                case 'group':
                    if ($column['search']['value'])
                        $model->whereIn('monitoring_group_id', explode(',', $column['search']['value']));
                    break;
                case 'url':
                    if ($column['search']['value'])
                        $model->whereIn('id', $this->getKeywordIdsWithNotValidateUrl($project->id, $region->id));
                    break;
                case 'dynamics':
                    if ($column['search']['value']) {
                        if ($column['search']['value'] == 'positive')
                            $model->where('dynamic', '>', 0);
                        elseif ($column['search']['value'] == 'negative')
                            $model->where('dynamic', '<', 0);
                    }
                    break;
            }
        }

        return $this;
    }

    private function updateDynamics()
    {
        $queries = $this->queries;
        foreach ($queries as $keyword) {
            $dynamics = 0;
            $model = $keyword->positions_view;
            if ($model && $model->count() > 1)
                $dynamics = ($model->last()->position - $model->first()->position);

            MonitoringKeyword::where('id', $keyword->id)->update(['dynamic' => $dynamics]);
        }

        return $this;
    }

    private function getKeywordIdsWithNotValidateUrl(int $projectId, int $regionId)
    {
        $lastDateUrlPosition = DB::table('monitoring_positions')
            ->select('monitoring_keyword_id', 'monitoring_searchengine_id', DB::raw('MAX(created_at) created_max'))
            ->whereNotNull('url')
            ->where('monitoring_searchengine_id', $regionId)
            ->groupBy('monitoring_keyword_id');

        $lastUrlPosition = DB::table('monitoring_positions')
            ->joinSub($lastDateUrlPosition, 'latest_url', function ($join) {
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
        if($keyword->project->users->find($user->id)){
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
        if($keyword->project->users->find($user->id)){

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
        if($keyword->project->users->find($user['id']))
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
