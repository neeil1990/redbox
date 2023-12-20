<?php


namespace App\Exports\Monitoring\Format;


use App\Exports\Monitoring\PositionsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportPDF implements IFormat
{
    protected $extension = '.pdf';

    public function download($data, $fileName)
    {
        return Excel::download(new PositionsExport($data), $fileName . $this->extension, \Maatwebsite\Excel\Excel::MPDF);
    }
}
