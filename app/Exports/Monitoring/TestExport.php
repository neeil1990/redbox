<?php

namespace App\Exports\Monitoring;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;

class TestExport implements FromView, ShouldAutoSize, WithDefaultStyles
{

    /**
     * @return View
     */
    public function view(): View
    {
        return view('monitoring.export.test');
    }

    public function defaultStyles(Style $defaultStyle)
    {
        // Or return the styles array
        return [
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
            ],
        ];
    }
}
