<?php

namespace App\Http\Controllers;

use App\Classes\Monitoring\StatisticsAdmin;
use App\Jobs;
use Illuminate\Http\Request;

class MonitoringAdminController extends Controller
{
    protected $jobs;

    public function __construct()
    {
        $this->middleware(['role:Super Admin|admin']);

        $this->jobs = (new Jobs())->positionsQueue();
    }

    public function statPage(Request $request)
    {
        if($request->ajax())
            return $this->getQueuesForDataTable($request);

        $statistics = $this->getStatCollection();

        return view('monitoring.admin.stat', compact('statistics'));
    }

    public function adminPage()
    {

        return view('monitoring.admin.admin');
    }

    public function getQueuesForDataTable(Request $request)
    {
        $dataTable = collect([]);

        $page = ($request->input('start') / $request->input('length')) + 1;
        $queues = $this->getQueues($request->input('length', 1), $page);

        foreach ($queues->getCollection() as $item){

            $dataTable->push([
                'id' => $item->id,
                'user' => $item->keyword->project->user->fullName,
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

    protected function getQueues($length, $page)
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
