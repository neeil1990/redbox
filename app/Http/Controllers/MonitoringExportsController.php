<?php

namespace App\Http\Controllers;

use App\Exports\Monitoring\PositionsExport;
use App\MonitoringProject;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Arr;

class MonitoringExportsController extends MonitoringKeywordsController
{
    private $groupColumnIndex = 4;
    private $removeColumns = ['checkbox', 'btn', 'url', 'group', 'target', 'dynamics', 'base', 'phrasal', 'exact'];
    private $type = '.pdf';

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

        foreach ($this->removeColumns as $col){
            if($request->has($col . 'Col'))
                unset($this->removeColumns[array_search($col, $this->removeColumns)]);
        }

        $this->columns->forget($this->removeColumns);
        $response = $this->setProjectID($id)->get($params);

        $file = $this->project['url'] . ' ' . $params['dates_range'] . $this->type;
        return Excel::download(new PositionsExport($response), $file, \Maatwebsite\Excel\Excel::MPDF);
    }

    public function edit($id)
    {
        $project = MonitoringProject::findOrFail($id);
        return view('monitoring.export.edit', compact('project'));
    }
}
