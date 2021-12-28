<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class HttpHeadersExport implements FromArray
{
    protected $items;
    /**
     * HttpHeadersExport constructor.
     * @param $items
     */
    public function __construct($items)
    {
        $this->items = $items;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $csv = [
            ['URL', 'CODE']
        ];

        foreach ($this->items as $item){
            $csv[] = [
              'URL' => $item['url'],
              'CODE' => $item['code'],
            ];
        }

        return $csv;
    }
}
