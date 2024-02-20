<?php

namespace App\Http\Controllers;

use App\MonitoringGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class MonitoringGroupsController extends Controller
{
    const OPTION_USERS = 'users_option';
    const OPTION_GROUPS = 'groups_option';

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
		$dataRequest = $request->input('data', []);

        foreach ($dataRequest as $data) {
            $collect = collect($data);

            $this->validation($collect);

            if($error = $this->getFieldErrors())
                return $error;

            switch ($request->input('action')) {
                case 'create':
                    $this->create($collect);
                    break;
                case 'edit':
                    $this->edit($collect);
                    break;
                case 'remove':
                    $this->remove($collect);
                    break;
            }
        }

        return $this->getDataTable();
    }

    public function getDataTable()
    {
        $collection = collect([]);
        $options = collect([]);

        $options->put(self::OPTION_GROUPS, $this->getGroupsOptions($this->project->groups));
        $options->put(self::OPTION_USERS, $this->getUsersOptions());

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
        foreach($users as $user){
            $name = $user['fullName'];
            $status = MonitoringProjectUserStatusController::getStatusById($user->pivot->status);
            $name .= ' (' . $status['name'] . ')';

            $options->push(collect(['label' => $name, 'value' => $user['id']]));
        }

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
    }

    private function edit(Collection $data)
    {
        $model = MonitoringGroup::where('id', $data['id'])->first();

        $model->update([
            'name' => $data['name']
        ]);

        $users = [];
        if ($data->has(self::OPTION_USERS))
            $users = $data[self::OPTION_USERS];

        $model->users()->sync($users);

        $group = (int) $data[self::OPTION_GROUPS];
        if($group > 0){
            $model->keywords->each(function($item) use ($group){
                $item->update(['monitoring_group_id' => $group]);
            });
        }
    }

    private function remove(Collection $data)
    {
        $group = $this->groups->where('id', $data['id'])->first();

        if($group->keywords->count() > 0)
            return collect(['error' => 'Перенесите запросы, чтобы удалить группу']);

        $group->delete();
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
