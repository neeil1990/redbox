<?php

namespace App\Http\Controllers;

use App\Exports\Monitoring\TestExport;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringExportsController extends MonitoringKeywordsController
{
    public function view()
    {
        $params = collect([
            'length' => 0,
            //'region_id' => 52,
        ]);
        $this->setProjectID(30);
        $response = $this->get($params);

        dd($response);
    }

    public function index()
    {
        $id = 30;
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->find($id);

        return Excel::download(new TestExport, 'test.pdf', \Maatwebsite\Excel\Excel::MPDF);
    }
}
