<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class MetaTagsFormExport implements FromCollection
{
    protected $data = [];

    public function __construct($data)
    {
        $this->data = collect($data);
    }

    public function collection()
    {
        $csv = $this->data->map(function ($item) {
            $obj = new \stdClass();

            $obj->url = $item['title'];
            foreach ($item['data'] as $tag => $val)
                $obj->$tag = is_array($val) ? implode(', ', $val) : "Нет проблем";

            return $obj;
        });

        if ($csv->first()) {
            $csv->prepend(array_keys(get_object_vars($csv->first())));
        }

        return $csv;
    }
}
