<?php

namespace App\Http\Controllers;

use App\Classes\Monitoring\Helper;
use App\Classes\Monitoring\ProjectDataTableUpdateDB;
use App\Common;
use App\Jobs\AutoUpdatePositionQueue;
use App\Jobs\PositionQueue;
use App\Location;
use App\MonitoringColumn;
use App\MonitoringCompetitor;
use App\MonitoringDataTableColumnsProject;
use App\MonitoringKeyword;
use App\MonitoringOccurrence;
use App\MonitoringPosition;
use App\MonitoringProject;
use App\MonitoringProjectColumnsSetting;
use App\MonitoringProjectSettings;
use App\MonitoringSearchengine;
use App\MonitoringSettings;
use App\SearchIndex;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use TheSeer\Tokenizer\Exception;

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
     * @return Response
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
        if (!$project)
            return collect(['status' => false]);

        $engines = $project->searchengines()->whereIn('id', $request->input('regions'))->get();

        foreach ($engines as $engine) {
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
        if (!$dataTable->count())
            $this->updateDataTableProjects();

        $model = $user->monitoringProjectsWithDataTable();

        $search = $request->input('search');
        if ($search = $search['value'])
            $model = $model->where('name', 'like', $search . '%');

        if ($order = Arr::first($request->input('order'))) {
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
        $projects->transform(function ($item) {
            $item->load(['searchengines' => function ($query) {
                $query->groupBy('engine');
            }]);

            $item->engines = $item->searchengines->pluck('engine')->map(function ($item) {
                return '<span class="badge badge-light"><i class="fab fa-' . $item . ' fa-sm"></i></span>';
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
        foreach ($engines as $engine) {
            $engine->data = collect([]);
            $positions = $engine->positions()->whereNotNull('position');

            if ($section)
                $positions->whereIn('monitoring_keyword_id', $section->keywords->pluck('id'));

            $positions = $positions->get();

            if ($positions->isNotEmpty()) {
                foreach ($this->subtractionMonths as $month) {
                    if ($grouped = $this->groupPositionsByMonth($positions, $month)) {
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

        foreach ($percents as $name => $percent) {
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
        $positions = $positions->groupBy('monitoring_keyword_id')->transform(function ($pos) {
            $p = $pos->sortByDesc('created_at')->values();

            if ($p->count() > 1)
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
        })->sortByDesc(function ($i, $k) {
            return Carbon::parse($k)->timestamp;
        });

        if ($subMonth === null)
            return $grouped;

        $carbon = Carbon::now()->subMonths($subMonth)->format($format);
        if ($grouped->has($carbon))
            return $grouped[$carbon];

        return null;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('monitoring.create');
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        /** @var User $user */
        $user = $this->user;

        /** @var MonitoringProject $project */
        $project = $user->monitoringProjects()->where('id', $id)->first();

        $navigations = $this->navigations($project);

        $length = $this->getLength($project->id);
        $lengthMenu = $this->getPaginationMenu();

        return view('monitoring.show', compact('navigations', 'project', 'length', 'lengthMenu'));
    }

    public function getPaginationMenu()
    {
        $lengthMenu = '[10,20,30,50]';

        if ($global = (new MonitoringSettings())->getValue('pagination_items'))
            $lengthMenu = '[' . $global . ']';

        return $lengthMenu;
    }

    public function getLength(int $projectId)
    {
        $lengthDefault = 100;

        if ($global = (new MonitoringSettings())->getValue('pagination_query'))
            $lengthDefault = $global;

        if ($length = $this->getSetting($projectId, 'length'))
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

    public function getPositionsForCalendars(Request $request)
    {
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->where('id', $request->input('projectId'))->first();
        $region = $project->searchengines();

        if ($request->input('regionId'))
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

    private function navigations(MonitoringProject $project): array
    {
        /** @var User $user */
        $user = $this->user;
        $countMonitoringProjects = $user->monitoringProjects()->count();
        $countBackLinkProjects = $user->backlingProjects()->count();
        $countCompetitors = count($project->competitors);

        $navigations = [
            ['h3' => $countMonitoringProjects, 'p' => 'Проекты', 'icon' => 'fas fa-bezier-curve', 'href' => route('monitoring.index'), 'bg' => 'bg-info'],
            ['h3' => $countCompetitors, 'p' => 'Мои конкуренты', 'small' => 'В разработке', 'icon' => 'fas fa-user-secret', 'href' => route('monitoring.competitors', $project->id), 'bg' => 'bg-success'],
            ['h3' => $countCompetitors + 1, 'p' => 'Анализ ТОП-100', 'small' => 'В разработке', 'icon' => 'fas fa-chart-pie', 'href' => route('monitoring.competitors.positions', $project->id), 'bg' => 'bg-warning'],
            ['h3' => '150', 'p' => 'План продвижения', 'small' => 'В разработке', 'icon' => 'far fa-check-square', 'href' => '#', 'bg' => 'bg-danger'],
            ['h3' => '150', 'p' => 'Аудит сайта', 'small' => 'В разработке', 'icon' => 'fas fa-tasks', 'href' => '#', 'bg' => 'bg-info'],
            ['h3' => $countBackLinkProjects, 'p' => 'Отслеживание ссылок', 'small' => '', 'icon' => 'fas fa-link', 'href' => route('backlink'), 'bg' => 'bg-purple-light'],
        ];

        return $navigations;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        /** @var User $user */
        $user = $this->user;
        $user->monitoringProjects()->where('id', $id)->delete();
    }

    public function monitoringCompetitors(MonitoringProject $project)
    {
        $countQuery = count($project->keywords);
        $navigations = $this->navigations($project);

        return view('monitoring.competitors', compact(
            'navigations',
            'countQuery',
            'project'
        ));
    }

    public function getCompetitorInfo(Request $request): JsonResponse
    {
        $competitors = MonitoringCompetitor::getCompetitors($request->all());

        return response()->json([
            'data' => $competitors
        ]);
    }

    public function getCompetitorsInfo(Request $request): JsonResponse
    {
        $competitors = MonitoringCompetitor::getCompetitors($request->all());

        return response()->json([
            'data' => $competitors
        ]);
    }

    public function addCompetitor(Request $request): ?JsonResponse
    {
        $project = MonitoringProject::findOrFail($request->projectId);

        if ($project->user->id !== Auth::id()) {
            return abort(403);
        }

        MonitoringCompetitor::insert([
            'monitoring_project_id' => $project->id,
            'url' => $request->url,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return response()->json([], 201);
    }

    public function removeCompetitor(Request $request): ?JsonResponse
    {
        $project = MonitoringProject::findOrFail($request->projectId);

        if ($project->user->id !== Auth::id()) {
            return abort(403);
        }

        MonitoringCompetitor::where('monitoring_project_id', $request->projectId)
            ->where('url', $request->url)
            ->delete();

        return response()->json([], 200);
    }

    public function competitorsPositions(MonitoringProject $project)
    {
        $competitors = Common::pullValue(MonitoringCompetitor::where('monitoring_project_id', $project->id)->get(['url']), 'url');
        $navigations = $this->navigations($project);

        return view('monitoring.rating', compact('project', 'competitors', 'navigations'));
    }

    public function getCompetitorsVisibility(Request $request): JsonResponse
    {
        $project = MonitoringProject::findOrFail($request->projectId);

        $keywords = Common::pullValue(MonitoringKeyword::where('monitoring_project_id', $project->id)->get(['query']), 'query');
        $competitors = Common::pullValue(MonitoringCompetitor::where('monitoring_project_id', $project->id)->get(['url']), 'url');
        array_unshift($competitors, $project->url);

        if (isset($request->region)) {
            $searchEngines = Common::pullValue(MonitoringSearchengine::where('id', '=', $request->region)->get(['lr'])->toArray(), 'lr');
        } else {
            $searchEngines = Common::pullValue(MonitoringSearchengine::where('monitoring_project_id', $project->id)->get(['lr'])->toArray(), 'lr');
        }

        $array = [];
        foreach ($keywords as $keyword) {
            foreach ($competitors as $competitor) {
                $array[$keyword][$competitor] = 0;
            }
        }

        foreach ($searchEngines as $searchEngine) {
            foreach ($keywords as $keyword) {
                $records = SearchIndex::where('query', $keyword)
                    ->where('lr', $searchEngine)
                    ->latest('created_at')
                    ->take(100)
                    ->get(['url', 'position', 'created_at']);

                foreach ($records as $record) {
                    $url = Common::domainFilter(parse_url($record['url'])['host']);
                    if (in_array($url, $competitors)) {
                        $array[$keyword][$url] = $record['position'];
                    }
                }
            }
        }

        return response()->json([
            'data' => $array
        ]);
    }
}
