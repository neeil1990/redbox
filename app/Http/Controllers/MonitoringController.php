<?php

namespace App\Http\Controllers;

use App\Classes\Monitoring\Helper;
use App\Classes\Monitoring\ProjectDataTableUpdateDB;
use App\Classes\Monitoring\Queues\PositionsDispatch;
use App\Common;
use App\Jobs\AutoUpdatePositionQueue;
use App\MonitoringColumn;
use App\MonitoringCompetitor;
use App\MonitoringDataTableColumnsProject;
use App\MonitoringKeyword;
use App\MonitoringPosition;
use App\MonitoringProject;
use App\MonitoringProjectColumnsSetting;
use App\MonitoringProjectSettings;
use App\MonitoringSearchengine;
use App\MonitoringSettings;
use App\SearchIndex;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $queue = new PositionsDispatch($user['id'], 'position_high');
        foreach ($engines as $engine) {
            foreach ($project->keywords as $query)
                $queue->addQueryWithRegion($query, $engine);
        }
        $queue->dispatch();

        return $queue->notify();
    }

    public function parsePositionsInProjectKeys(Request $request)
    {
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->where('id', $request->input('projectId'))->first();
        $keywords = $project->keywords()->whereIn('id', $request->input('keys'))->get();
        $engine = $project->searchengines()->find($request->input('region'));

        $queue = new PositionsDispatch($user['id'], 'position_high');
        foreach ($keywords as $keyword)
            $queue->addQueryWithRegion($keyword, $engine);

        $queue->dispatch();

        return $queue->notify();
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
            ['h3' => $countCompetitors, 'p' => 'Мои конкуренты', 'small' => '', 'icon' => 'fas fa-user-secret', 'href' => route('monitoring.competitors', $project->id), 'bg' => 'bg-success'],
            ['h3' => '150', 'p' => 'Анализ ТОП-100', 'small' => 'В разработке', 'icon' => 'fas fa-chart-pie', 'href' => route('monitoring.competitors.positions', $project->id), 'bg' => 'bg-warning'],
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

        return view('monitoring.competitors.index', compact(
            'navigations',
            'countQuery',
            'project'
        ));
    }

    public function getCompetitorsInfo(Request $request): string
    {
        return MonitoringCompetitor::getCompetitors($request->all());
    }

    public function addCompetitor(Request $request): ?JsonResponse
    {
        $project = MonitoringProject::findOrFail($request->projectId);

        if ($project->user->id !== Auth::id()) {
            return abort(403);
        }

        $parse = parse_url($request->url);
        $domain = $parse['host'] ?? $parse['path'];
        $url = Common::domainFilter($domain);

        $record = MonitoringCompetitor::where('monitoring_project_id', $project->id)
            ->where('url', $url)
            ->first();

        if (empty($record)) {
            MonitoringCompetitor::insert([
                'monitoring_project_id' => $project->id,
                'url' => $url,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return response()->json([], 201);
    }

    public function addCompetitors(Request $request): ?JsonResponse
    {
        $project = MonitoringProject::findOrFail($request->projectId);
        $urls = [];
        if ($project->user->id !== Auth::id()) {
            return abort(403);
        }

        foreach ($request->domains as $domain) {
            if ($domain === null) {
                continue;
            }
            $parse = parse_url($domain);
            $domain = $parse['host'] ?? $parse['path'];
            $url = Common::domainFilter($domain);
            $record = MonitoringCompetitor::where('monitoring_project_id', $project->id)
                ->where('url', $url)
                ->first();

            if (empty($record)) {
                $urls[] = $url;
                MonitoringCompetitor::insert([
                    'monitoring_project_id' => $project->id,
                    'url' => $url,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        return response()->json([
            'urls' => $urls
        ], 201);
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
        $competitors = MonitoringCompetitor::where('monitoring_project_id', $project->id)->pluck('url')->toArray();
        $navigations = $this->navigations($project);

        return view('monitoring.competitors.history', compact('project', 'competitors', 'navigations'));
    }

    public function getStatistics(Request $request): JsonResponse
    {
        $statistics = MonitoringCompetitor::calculateStatistics($request->all());

        return response()->json([
            'visibility' => $statistics['visibility'],
            'statistics' => $statistics['statistics'],
        ]);
    }

    public function competitorsHistoryPositions(Request $request): JsonResponse
    {
        $project = MonitoringProject::findOrFail($request->projectId);
        $keywords = MonitoringKeyword::where('monitoring_project_id', $project->id)->pluck('query', 'id')->toArray();
        $competitors = MonitoringCompetitor::where('monitoring_project_id', $project->id)->pluck('url')->toArray();
        $lr = MonitoringSearchengine::where('id', '=', $request->region)->pluck('lr')->toArray()[0];
        array_unshift($competitors, $project->url);

        $records = [];
        $results = [];

        $range = explode(' - ', $request->dateRange);
        $period = CarbonPeriod::create($range[0], $range[1]);
        $dates = [];

        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        foreach ($dates as $date) {
            $results = DB::table('search_indices')
                ->where('created_at', 'like', "%$date%")
                ->where('lr', '=', $lr)
                ->whereIn('query', $keywords)
                ->where('position', '<=', 100)
                ->orderBy('id', 'desc')
                ->limit(count($keywords) * 100)
                ->get(['url', 'position', 'created_at', 'query'])
                ->toArray();

            foreach ($results as $result) {
                $records[$date][$result->query][$lr][] = $result;
            }
        }

        foreach ($records as $date => $queries) {
            foreach ($queries as $lrs) {
                foreach ($lrs as $positions) {
                    if (count($positions) === 0) {
                        continue;
                    }
                    foreach ($competitors as $competitor) {
                        foreach ($positions as $keyPos => $result) {
                            $url = Common::domainFilter(parse_url($result->url)['host']);
                            if ($competitor === $url) {
                                $results[$date][$competitor]['positions'][] = $result->position;
                                continue 2;
                            } else if (array_key_last($positions) === $keyPos) {
                                $results[$date][$competitor]['positions'][] = 101;
                            }
                        }
                    }
                }
            }
        }

        foreach ($results as $date => $result) {
            foreach ($result as $domain => $data) {
                try {
                    $results[$date][$domain]['avg'] = round(array_sum($data['positions']) / count($keywords), 2);
                    $results[$date][$domain]['top_3'] = Common::percentHitIn(3, $data['positions']);
                    $results[$date][$domain]['top_10'] = Common::percentHitIn(10, $data['positions']);
                    $results[$date][$domain]['top_100'] = Common::percentHitIn(100, $data['positions']);
                } catch (\Throwable $e) {

                }

            }
        }


        return response()->json([
            'data' => array_reverse($results)
        ]);
    }
}
