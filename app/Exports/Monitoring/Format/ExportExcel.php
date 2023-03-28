<?php


namespace App\Exports\Monitoring\Format;

use App\Exports\Monitoring\PositionsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportExcel implements IFormat
{
    protected $extension = '.xls';

    public function download($data, $fileName)
    {
        return Excel::download(new PositionsExport($data), $fileName . $this->extension, \Maatwebsite\Excel\Excel::XLS);
    }
}
