<?php


namespace App\Exports\Monitoring;


use App\Exports\Monitoring\Format\ExportCsv;
use App\Exports\Monitoring\Format\ExportExcel;
use App\Exports\Monitoring\Format\ExportHtml;
use App\Exports\Monitoring\Format\ExportPDF;
use App\Exports\Monitoring\Format\IFormat;

class PositionsExportFactory
{
    private IFormat $export;

    public function createExport(string $extension = 'pdf'): IFormat
    {
        switch ($extension) {
            case "xls":
                $this->export = new ExportExcel();
                break;
            case "html":
                $this->export = new ExportHtml();
                break;
            case "csv":
                $this->export = new ExportCsv();
                break;
            default:
                $this->export = new ExportPDF();
        }

        return $this->export;
    }
}
