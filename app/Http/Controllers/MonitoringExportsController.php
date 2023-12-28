<?php

namespace App\Http\Controllers;

use App\Exports\Monitoring\Format\ExportCsv;
use App\Exports\Monitoring\Format\ExportExcel;
use App\Exports\Monitoring\Format\ExportHtml;
use App\Exports\Monitoring\Format\ExportPDF;
use App\Exports\Monitoring\Format\IFormat;
use App\Exports\Monitoring\PositionsExportFactory;
use App\MonitoringProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MonitoringExportsController extends MonitoringKeywordsController
{
    private $budget = 0;
    private $groupColumnIndex = 4;
    private $removeColumns = [
        'checkbox',
        'btn',
        'url',
        'group',
        'dynamics',
        'base',
        'phrasal',
        'exact',
        'price_top_1',
        'price_top_3',
        'price_top_5',
        'price_top_10',
        'price_top_20',
        'price_top_50',
        'price_top_100',
        'days_top_1',
        'days_top_3',
        'days_top_5',
        'days_top_10',
        'days_top_20',
        'days_top_50',
        'days_top_100',
    ];

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
        $export = new PositionsExportFactory();
        $this->setFormat($export->createExport($extension));

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

        $this->setProjectID($id)->dataPrepare($params)->columns->forget($this->removeColumns);
        $this->budget = $this->project->budget;
        $response = $this->generateDataTable();

        if($request['mode'] == 'finance')
            $this->setTotalSum($response);

        $this->urlColumn($response);

        $file = $this->project['url'] . ' ' . $params['dates_range'];
        return $this->downloadFile($response, $file, $request['format']);
    }

    public function edit($id)
    {
        $project = MonitoringProject::findOrFail($id);
        return view('monitoring.export.edit', compact('project'));
    }

    private function setTotalSum(&$response)
    {
        $total = $response['data']->pluck('mastered')->sum();
        $count = $response['columns']->count();

        $response['data']->push(collect(['Выведено фраз на сумму:', $total])->pad(-$count, ''));
        $response['data']->push(collect(['Максимальный бюджет:', $this->budget])->pad(-$count, ''));
    }

    private function urlColumn(Collection &$collection)
    {
        $collection['data']->transform(function($item){
            if($item->has('url')){
                $url = $item['url'];
                $doc = new \DOMDocument();
                $doc->loadHTML($url);
                $a = $doc->getElementsByTagName('a');
                $item['url'] = strip_tags($a[0]->getAttribute('data-content'));
            }
            return $item;
        });
    }
}
