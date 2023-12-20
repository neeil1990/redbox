<?php


namespace App\Exports\Monitoring\Format;

use App\Exports\Monitoring\PositionsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportCsv implements IFormat
{
    protected $extension = '.csv';

    public function download($data, $fileName)
    {
        return Excel::download(new PositionsExport($data), $fileName . $this->extension, \Maatwebsite\Excel\Excel::CSV);
    }
}
