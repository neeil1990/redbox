<?php

namespace App\Http\Controllers;


use App\Classes\Monitoring\Queues\OccurrenceDispatch;
use App\Classes\Monitoring\Queues\PositionsDispatch;
use App\Jobs\OccurrenceQueue;
use App\MonitoringProject;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringOccurrenceController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });
    }

    public function update(Request $request)
    {
        return $this->updateByProjectId($request->input('id'));
    }

    protected function updateByProjectId($id)
    {
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->find($id);
        $regions = $project->searchengines->where('engine', 'yandex');

        $queue = new OccurrenceDispatch($user['id'], 'high');
        foreach ($regions as $region){
            foreach ($project->keywords as $query)
                $queue->addQueryWithRegion($query, $region);
        }
        $queue->dispatch();

        return $queue->notify();
    }

}
