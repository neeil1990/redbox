<?php

namespace App\Http\Controllers;

use App\Exports\Monitoring\PositionsExport;
use App\MonitoringProject;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringExportsController extends MonitoringKeywordsController
{
    private $groupColumnIndex = 4;

    public function download(Request $request, $id)
    {
        $index = $this->groupColumnIndex;
        $date = implode(' - ', [Carbon::parse($request['startDate'])->locale('ru')->toDateString(), Carbon::parse($request['endDate'])->locale('ru')->toDateString()]);
        $params = collect([
            'length' => 0,
            'mode_range' => $request['mode'],
            'region_id' => $request['region'],
            'dates_range' => $date,
            'columns' => [
                $index => [
                    'data' => 'group',
                    'search' => [
                        'value' => $request['group']
                    ]
                ]
            ],
        ]);

        $this->columns->forget(['checkbox', 'btn', 'url', 'target', 'base', 'phrasal', 'exact']);
        $response = $this->setProjectID($id)->get($params);

        return Excel::download(new PositionsExport($response), 'positions.pdf', \Maatwebsite\Excel\Excel::MPDF);
    }

    public function edit($id)
    {
        $project = MonitoringProject::findOrFail($id);
        return view('monitoring.export.edit', compact('project'));
    }
}
