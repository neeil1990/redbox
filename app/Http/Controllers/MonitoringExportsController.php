<?php

namespace App\Http\Controllers;

use App\Exports\Monitoring\AttributeExport;
use App\Exports\Monitoring\ColumnEditor;
use App\Exports\Monitoring\Format\IFormat;
use App\Exports\Monitoring\PositionsExportFactory;
use App\MonitoringProject;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitoringExportsController extends MonitoringKeywordsController
{
    const QUERY_INDEX = 1;
    const GROUP_INDEX = 2;

    private $removeColumns = [
        'checkbox',
        'btn',
        'url',
        'group',
        'target_url',
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
        $date = implode(' - ', [
            Carbon::parse($request['startDate'])->locale('ru')->toDateString(),
            Carbon::parse($request['endDate'])->locale('ru')->toDateString()
        ]);

        $params = collect([
            'length' => 0,
            'mode_range' => $request['mode'],
            'region_id' => $request['region'],
            'dates_range' => $date,
            'columns' => [
                self::GROUP_INDEX => [
                    'data' => 'group',
                    'search' => [
                        'value' => ($request['group']) ? implode(',', $request['group']) : null
                    ]
                ],
                self::QUERY_INDEX => [
                    'data' => 'query',
                    'search' => [
                        'value' => null
                    ]
                ]
            ],
            'order' => [
                [
                    'column' => $request['order']['column'],
                    'dir' => $request['order']['dir'],
                ]
            ],
            'offset' => $request['offset'] ?? [],
        ]);

        foreach ($this->removeColumns as $col) {
            if ($request->has($col . 'Col')) {
                unset($this->removeColumns[array_search($col, $this->removeColumns)]);
            }
        }

        $this->setProjectID($id)
            ->dataPrepare($params)
            ->columns
            ->forget($this->removeColumns);

        $response = $this->generateDataTable();

        $editor = (new ColumnEditor($response))->setColumns($request);

        $response['columns'] = $editor->getColumns();
        $response['data'] = $editor->getData();

        $attribute = new AttributeExport($response, $request);
        $attribute->setBudget($this->project->budget);
        $attribute->execute();

        $file = $this->project['url'] . ' ' . $params['dates_range'];
        return $this->downloadFile($response, $file, $request['format']);
    }

    public function edit($id)
    {
        $project = MonitoringProject::findOrFail($id);
        return view('monitoring.export.edit', compact('project'));
    }
}
