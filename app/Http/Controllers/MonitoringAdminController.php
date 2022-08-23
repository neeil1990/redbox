<?php

namespace App\Http\Controllers;

use App\Classes\Monitoring\StatisticsAdmin;
use App\Jobs;
use App\MonitoringProject;
use App\User;
use Illuminate\Http\Request;

class MonitoringAdminController extends Controller
{
    protected $jobs;
    protected $users;
    protected $projects;

    public function __construct()
    {
        $this->middleware(['role:Super Admin|admin']);

        $this->jobs = (new Jobs())->positionsQueue();
        $this->users = new User();
        $this->projects = new MonitoringProject();
    }

    public function statPage(Request $request)
    {
        if($request->ajax())
            return $this->getQueuesForDataTable($request);

        $statistics = $this->getStatCollection();

        $users = $this->users->all()->pluck('fullName', 'id');
        $sites = $this->projects->all()->pluck('url', 'url');

        return view('monitoring.admin.stat', compact('statistics', 'users', 'sites'));
    }

    public function adminPage()
    {

        return view('monitoring.admin.admin');
    }

    public function deleteQueues(Request $request)
    {
        if($request->has('delete_queues')){

            $this->jobs->delete();
            flash()->overlay(__('Delete successfully'), __('Delete queues'))->success();
        }else{

            $queues = collect([]);
            if($request->filled(['user', 'project'])){

                $queues = $this->jobs->get()->filter(function($item) use ($request){

                    $jobData = unserialize($item->payload['data']['command']);
                    $keyword = $jobData->getModel();

                    return ($keyword->project->url == $request->input('project') && $keyword->project->user->id == $request->input('user'));
                });
            }else{

                $params = collect($request->only(['user', 'project']))->filter();
                if($params->isNotEmpty()){

                    $queues = $this->jobs->get()->filter(function($item) use ($params){

                        $jobData = unserialize($item->payload['data']['command']);
                        $keyword = $jobData->getModel();

                        if (array_key_exists('user', $params->toArray()))
                            return ($keyword->project->user->id == $params['user']);

                        if (array_key_exists('project', $params->toArray()))
                            return ($keyword->project->url == $params['project']);

                    });
                }
            }

            if($queues->isNotEmpty()){
                $this->jobs->whereIn('id', $queues->pluck('id'))->delete();
                flash()->overlay('Удалено ' . $queues->count(), __('Delete queues'))->success();
            }
        }

        return redirect()->back();
    }

    public function getQueuesForDataTable(Request $request)
    {
        $dataTable = collect([]);

        $page = ($request->input('start') / $request->input('length')) + 1;
        $queues = $this->getQueuesOnPage($request->input('length', 1), $page);

        foreach ($queues->getCollection() as $item){

            $dataTable->push([
                'id' => $item->id,
                'user' => $item->keyword->project->user->fullName,
                'email' => $item->keyword->project->user->email,
                'site' => $item->keyword->project->url,
                'group' => $item->keyword->group->name,
                'params' => $item->keyword->project->searchengines->implode('lr', ', '),
                'query' => $item->keyword->query,
                'priority' => ($item->queue === 'position_high') ? __('High') : __('Low'),
                'created_at' => $item->created_at->format('d.m.Y H:i:s'),
                'attempts' => $item->attempts,
            ]);
        }

        return collect([
            'data' => $dataTable,
            'draw' => $request->input('draw'),
            'recordsFiltered' => $queues->total(),
            'recordsTotal' => $queues->total(),
        ]);
    }

    protected function getQueuesOnPage($length, $page)
    {

        $jobs = $this->jobs->paginate($length, ['*'], 'page', $page);

        $jobs->getCollection()->transform(function ($item) {

            $jobData = unserialize($item->payload['data']['command']);
            $item->keyword = $jobData->getModel();

            return $item;
        });

        return $jobs;
    }

    protected function getStatCollection()
    {
        $statistics = collect([]);

        $stat = new StatisticsAdmin();

        $statistics->push($stat->getCountOfCheckUpForCurrentDay());
        $statistics->push($stat->getCountOfCheckUpForCurrentMonth());
        $statistics->push($stat->getCountOfErrorsForCurrentDay());
        $statistics->push($stat->getCountOfProjects());
        $statistics->push($stat->getCountOfTasksInQueue());

        return $statistics->filter()->values();
    }
}
