<?php

namespace App\Exports;

use App\MetaTagsHistory;
use Maatwebsite\Excel\Concerns\FromCollection;

class MetaTagsHistoriesExport implements FromCollection
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $history = MetaTagsHistory::findOrFail($this->id);
        $history_data = collect(json_decode($history->data));

        $csv = $history_data->map(function ($item, $key) {

            $obj = new \stdClass();

            $obj->url = $item->title;
            foreach ($item->data as $tag => $val)
                $obj->$tag = is_array($val) ? implode(', ', $val) : "Нет проблем";

            return $obj;
        });

        if($csv->first())
            $csv->prepend(array_keys(get_object_vars($csv->first())));

        return $csv;
    }
}
