<?php

namespace App\Exports\Monitoring;

use Illuminate\Contracts\View\View;
use Iterator;
use Maatwebsite\Excel\Concerns\FromIterator;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Events\BeforeExport;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PositionsExport implements FromView, WithDefaultStyles, WithEvents, ShouldAutoSize, WithStyles, WithTitle
{
    protected $data;
    private $green = "#99e4b9";
    private $yellow = "#fbe1df";

    public function __construct($data)
    {
        $this->data = $data;
        $this->dataFormat();
    }

    private function dataFormat()
    {
        $data = $this->data['data'];
        foreach ($data as $ek => $el){
            $target = trim(strip_tags($el['target']));

            foreach ($el as $fk => $field){
                if(preg_match('/data-position/', $field)){
                    $col = $this->formatPosition($field);
                    $col['color'] = null;

                    if($target >= (int)$col[0]){
                        $col['color'] = $this->green;
                    }else{
                        $ck = 'col_' . (filter_var($fk, FILTER_SANITIZE_NUMBER_INT) + 1);
                        if(isset($el[$ck])){
                            $p = $this->formatPosition($el[$ck]);
                            if($target >= (int)$p[0]){
                                $col['color'] = $this->yellow;
                            }
                        }
                    }
                    $this->data['data'][$ek][$fk] = $col;
                }
                else
                    $this->data['data'][$ek][$fk] = trim(strip_tags($field));
            }
        }
    }

    private function formatPosition(string $field): array
    {
        return array_values(array_filter(explode(' ', trim(strip_tags($field)))));
    }

    /**
     * @return View
     */
    public function view(): View
    {
        $data = $this->data;
        return view('monitoring.export.index', compact('data'));
    }

    public function defaultStyles(Style $defaultStyle)
    {
        // Or return the styles array
        return [
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'indent' => 1,
            ],
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function(BeforeExport $event) {
                $properties = $event->writer->getProperties();
                //$properties->setTitle('RedBox');
            },

            AfterSheet::class => function(AfterSheet $event) {
                //$event->sheet->getDelegate()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_NONE,
                    ],
                ],
            ],
            2 => [
                'font' => ['bold' => true],
            ],

        ];
    }

    public function title(): string
    {
        return 'RedBox title';
    }
}
