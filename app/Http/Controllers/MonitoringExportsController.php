<?php

namespace App\Http\Controllers;

use App\Exports\Monitoring\Format\ExportCsv;
use App\Exports\Monitoring\Format\ExportExcel;
use App\Exports\Monitoring\Format\ExportHtml;
use App\Exports\Monitoring\Format\ExportPDF;
use App\Exports\Monitoring\Format\IFormat;
use App\MonitoringProject;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitoringExportsController extends MonitoringKeywordsController
{
    private $groupColumnIndex = 4;
    private $removeColumns = ['checkbox', 'btn', 'url', 'group', 'target', 'dynamics', 'base', 'phrasal', 'exact'];

    /**
     * @var IFormat
     */
    private IFormat $format;

    public function setFormat(IFormat $format): void
    {
        $this->format = $format;
    }

    public function downloadFile($data, $fileName, $extension = 'pdf')
    {
        switch ($extension) {
            case "xls":
                $this->setFormat(new ExportExcel());
                break;
            case "html":
                $this->setFormat(new ExportHtml());
                break;
            case "csv":
                $this->setFormat(new ExportCsv());
                break;
            default:
                $this->setFormat(new ExportPDF());
        }

        return $this->format->download($data, $fileName);
    }

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
                        'value' => ($request['group']) ? implode(',', $request['group']) : null
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

        $file = $this->project['url'] . ' ' . $params['dates_range'];
        return $this->downloadFile($response, $file, $request['format']);
    }

    public function edit($id)
    {
        $project = MonitoringProject::findOrFail($id);
        return view('monitoring.export.edit', compact('project'));
    }
}
