<?php

namespace App\Http\Controllers;

use App\Exports\Monitoring\TestExport;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringExportsController extends MonitoringKeywordsController
{
    public function index($id)
    {
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->find($id);
        $region = $project->searchengines->first();

        $params = collect([
            'length' => 0,
            'region_id' => $region['id'],
        ]);

        $this->columns->forget(['checkbox', 'btn', 'url']);
        $response = $this->setProjectID($id)->get($params);

        return Excel::download(new TestExport($response), 'positions.pdf', \Maatwebsite\Excel\Excel::MPDF);
    }
}
