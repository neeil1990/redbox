<?php


namespace App\Exports\Monitoring;

use App\Helpers\CollectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ColumnEditor
{
    protected $columns;
    protected $data;

    public function __construct($collection)
    {
        $this->columns = $collection['columns'];
        $this->data = $collection['data'];
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setColumns(Request $request)
    {
        if($request->input('days_top_10_sumCol')) {
            $this->daysTop10Sum();
        }

        return $this;
    }

    private function daysTop10Sum()
    {
        $beforeCol = 'mastered';
        $col = 'days_top_10_sum';

        $this->columns = CollectionHelper::appendBefore($this->columns, $col, __('Сумма дней в топ 10'), $beforeCol);

        $this->data->transform(function ($item) use ($col, $beforeCol) {
            $sum = 0;

            foreach ([1, 3, 5, 10] as $top) {
                if (isset($item["days_top_$top"])) {
                    $sum += $item["days_top_$top"];
                }
            }

            $item = CollectionHelper::appendBefore($item, $col, $sum, $beforeCol);

            return $item;
        });
    }
}
