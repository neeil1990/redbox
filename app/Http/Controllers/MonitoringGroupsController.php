<?php

namespace App\Http\Controllers;

use App\MonitoringGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class MonitoringGroupsController extends Controller
{
    protected $user;

    protected $project;
    protected $groups;
    protected $fieldErrors;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });

        $this->fieldErrors = collect([]);
    }

    public function index(Request $request, $id)
    {
        $this->fillFields($request, $id);

        if($request->ajax())
            return $this->getDataTable();


        return view('monitoring.groups.index');
    }

    public function action(Request $request, $id)
    {
        $this->fillFields($request, $id);

        $data = collect($request->input('data'))->collapse();

        $this->validation($data);

        if($error = $this->getFieldErrors())
            return $error;

        switch ($request->input('action')) {
            case 'create':
                return $this->create($data);
                break;
            case 'edit':
                return $this->edit($data);
                break;
            case 'remove':
                return $this->remove($data);
                break;
        }
    }

    public function getDataTable()
    {
        $collection = collect([]);
        $options = collect([]);

        $options->put('groups', $this->getGroupsOptions($this->project->groups));
        $options->put('users', $this->getUsersOptions());

        $collection->put('data', $this->groups);
        $collection->put('options', $options);

        return $collection;
    }

    public function getGroupsOptions(Collection $collection)
    {
        if($collection->isEmpty())
            return false;

        $options = collect([]);
        $options->push(collect(['label' => "Не переносить", 'value' => 0]));
        foreach($collection->pluck('name', 'id') as $id => $name){
            $options->push(collect(['label' => $name, 'value' => $id]));
        }

        return $options;
    }

    public function getUsersOptions()
    {
        $users = $this->project->users;
        if($users->isEmpty())
            return false;

        $options = collect([]);
        foreach($users->pluck('fullName', 'id') as $id => $name)
            $options->push(collect(['label' => $name, 'value' => $id]));

        return $options;
    }

    private function fillFields(Request $request, $projectId)
    {
        $user = $this->user;
        $this->project = $user->monitoringProjects()->find($projectId);
        $model = $this->project->groups();

        if($request->has('search')){
            $search = $request->input('search');
            if($search = $search['value'])
                $model->where('name', 'like', '%' . $search . '%');
        }

        $this->groups = $model->get();

        $this->groups->transform(function($item){

            $item->DT_RowId = "row_" . $item->id;
            $item->queries = $item->keywords->count();
            $item->created = $item->created_at->diffForHumans();

            return $item;
        });

        if($request->has('order')){
            $order = collect($request->input('order'))->first();
            $column = $request->input('columns')[$order['column']]['name'];

            if($order['dir'] == 'asc')
                $this->groups = $this->groups->sortBy($column)->values();
            else
                $this->groups = $this->groups->sortByDesc($column)->values();
        }
    }

    private function create(Collection $data)
    {
        $this->project->groups()->create([
            'type' => 'keyword',
            'name' => $data['name'],
        ]);

        return $this->getDataTable();
    }

    private function edit(Collection $data)
    {
        $model = MonitoringGroup::where('id', $data['id'])->first();

        $model->update([
            'name' => $data['name']
        ]);

        $users = [];
        if ($data->has('users'))
            $users = $data['users'];

        $model->users()->sync($users);

        $group = (int) $data['groups'];
        if($group > 0){
            $model->keywords->each(function($item) use ($group){
                $item->update(['monitoring_group_id' => $group]);
            });
        }

        return $this->getDataTable();
    }

    private function remove(Collection $data)
    {
        $group = $this->groups->where('id', $data['id'])->first();

        if($group->keywords->count() > 0)
            return collect(['error' => 'Перенесите запросы, чтобы удалить группу']);

        $group->delete();

        return $this->getDataTable();
    }

    private function validation($data)
    {
        if(!strlen(trim($data['name'])))
            $this->setFieldErrors('name', 'Поле "Группа" обязательное');

    }

    public function getFieldErrors()
    {
        if($this->fieldErrors->isEmpty())
            return false;

        return collect([
            'fieldErrors' => $this->fieldErrors,
        ]);
    }

    public function setFieldErrors($field, $text)
    {
        $this->fieldErrors->push(['name' => $field, 'status' => $text]);
    }

    public function store(Request $request)
    {
        $model = new MonitoringGroup();
        return $model->create($request->all());
    }
}
