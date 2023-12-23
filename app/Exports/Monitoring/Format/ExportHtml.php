<?php


namespace App\Exports\Monitoring\Format;

use App\Exports\Monitoring\PositionsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportHtml implements IFormat
{
    protected $extension = '.html';

    public function download($data, $fileName)
    {
        return Excel::download(new PositionsExport($data), $fileName . $this->extension, \Maatwebsite\Excel\Excel::HTML);
    }
}
