<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringProjectCreatorController extends Controller
{
    protected $user;
    protected $project;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });
    }

    public function createProject(Request $request)
    {
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->create([
            'status' => 1,
            'name' => $request->input('name'),
            'url' => $request->input('url'),
        ]);

        if(!$project)
            return false;

        return $project['id'];
    }

    public function updateProject(Request $request)
    {
        $id = $request->input('id');

        /** @var User $user */
        $user = $this->user;
        $user->monitoringProjects()->find($id)->update([
            'name' => $request->input('name'),
            'url' => $request->input('url'),
        ]);

        return $id;
    }

    public function editProject(Request $request)
    {
        $id = $request->input('id');

        /** @var User $user */
        $user = $this->user;

        return $user->monitoringProjects()->find($id);
    }

    public function actionQueries(Request $request)
    {
        /** @var User $user */
        $user = $this->user;
        $this->project = $user->monitoringProjects()->find($request->input('id'));

        switch ($request->input('action')) {
            case 'create':
                return $this->createQueries($request);
                break;
            case 'edit':
                return $this->editQueries($request);
                break;
            case 'remove':
                return $this->removeQueries($request);
                break;
        }
    }

    public function getQueries(Request $request)
    {
        $collections = collect([]);
        $id = $request->input('id');

        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->find($id);

        if(!$project)
            return collect([
                'data' => $collections,
                'recordsFiltered' => 0,
                'recordsTotal' => 0,
            ]);

        $page = ($request->input('start') / $request->input('length')) + 1;
        $keywords = $project->keywords()->paginate($request->input('length', 1), ['*'], 'page', $page);

        foreach ($keywords as $q){

            $collections->push([
                'DT_RowId' => "row_" . $q->id,
                'query' => $q->query,
                'page' => $q->page,
                'group' => $q->group->name,
                'target' => $q->target,
            ]);
        }

        $data = collect([
            'data' => $collections,
            'draw' => $request->input('draw'),
            'recordsFiltered' => $keywords->total(),
            'recordsTotal' => $keywords->total(),
        ]);

        return $data;
    }

    public function getCompetitors(Request $request)
    {
        $id = $request->input('id');

        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->find($id);

        return implode(PHP_EOL, $project->competitors->pluck('url')->toArray());
    }

    public function createCompetitors(Request $request)
    {
        $id = $request->input('id');
        $competitors = preg_split("/\r\n|\n|\r/", $request->input('domains'));

        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->find($id);

        foreach ($competitors as $competitor){
            $project->competitors()->firstOrCreate([
                'url' => $competitor,
            ]);
        }
    }

    public function actionRegion(Request $request)
    {
        /** @var User $user */
        $user = $this->user;
        $this->project = $user->monitoringProjects()->find($request->input('id'));

        switch ($request->input('action')) {
            case 'get':
                return $this->getRegions($request);
                break;
            case 'create':
                return $this->createRegion($request);
                break;
            case 'update':
                return $this->updateRegion($request);
                break;
            case 'remove':
                return $this->removeRegion($request);
                break;
        }
    }

    private function getRegions(Request $request)
    {
        $this->project->searchengines->transform(function ($item){
            $item->name = $item->location->name;

            return $item;
        });

        return $this->project->searchengines;
    }

    private function createRegion(Request $request)
    {
        $this->project->searchengines()->firstOrCreate([
            'engine' => $request->input('engine'),
            'lr' => $request->input('lr'),
        ]);
    }

    private function updateRegion(Request $request)
    {
        $this->project->searchengines()->update([
            'auto_update' => false,
            'time' => null,
            'weekdays' => null,
            'monthday' => null,
        ]);

        $data = $request->input('data');
        foreach ($data as $item){
            $this->project->searchengines()->find($item['id'])->update([
                'auto_update' => true,
                $item['name'] => $item['val'],
            ]);
        }
    }

    private function removeRegion(Request $request)
    {
        $this->project->searchengines()->where(['engine' => $request->input('engine'), 'lr' => $request->input('lr')])->delete();
    }

    private function createQueries(Request $request)
    {
        $data = $request->input('data');
        foreach ($data as $item){
            $item['group'] = $this->firstOrCreateGroup($item['group']);
            $this->createKeywords($item);
        }

        return $this->emptyDataCollection();
    }

    private function editQueries(Request $request)
    {
        $data = $request->input('data');
        foreach ($data as $row => $item){
            $id = $this->stringToInt($row);
            $item['group'] = $this->firstOrCreateGroup($item['group']);
            $this->updateKeywords($id, $item);
        }

        return $this->emptyDataCollection();
    }

    private function removeQueries(Request $request)
    {
        $data = $request->input('data');
        foreach ($data as $item){
            $queryId = $this->stringToInt($item['DT_RowId']);
            $this->project->keywords()->find($queryId)->delete();
        }

        $this->deleteEmptyGroups();

        return $this->emptyDataCollection();
    }

    private function updateKeywords(int $id, array $data)
    {
        if(!$this->project)
            throw new ModelNotFoundException("Not exist MonitoringProject model");

        $this->project->keywords()->find($id)->update([
            'monitoring_group_id' => $data['group'],
            'query' => $data['query'],
            'page' => $data['page'],
            'target' => $data['target'],
        ]);
    }

    private function createKeywords($data)
    {
        if(!$this->project)
            throw new ModelNotFoundException("Not exist MonitoringProject model");

        $this->project->keywords()->create([
            'monitoring_group_id' => $data['group'],
            'query' => $data['query'],
            'page' => $data['page'],
            'target' => $data['target'],
        ]);
    }

    private function firstOrCreateGroup($name = null)
    {
        if(!$this->project)
            throw new ModelNotFoundException("Not exist MonitoringProject model");

        if(!trim(strip_tags($name)))
            $name = 'Основная';

        $group = $this->project->groups()->firstOrCreate([
            'type' => 'keyword',
            'name' => $name
        ]);

        return $group['id'];
    }

    private function deleteEmptyGroups()
    {
        if(!$this->project)
            throw new ModelNotFoundException("Not exist MonitoringProject model");

        foreach ($this->project->groups as $group){
            if(!$group->keywords->count())
                $group->delete();
        }
    }

    private function stringToInt(string $str)
    {
        return intval(filter_var($str, FILTER_SANITIZE_NUMBER_INT));
    }

    private function emptyDataCollection()
    {
        return collect([
            'data' => []
        ]);
    }
}
