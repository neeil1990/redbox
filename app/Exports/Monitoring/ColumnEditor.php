<?php


namespace App\Exports\Monitoring;


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

    public function setDaysTop10SumColumn()
    {
        $col = 'days_top_10_sum';

        $this->columns->put($col, 'Сумма дней в топ 10');

        $this->data->transform(function ($item) use ($col) {
            $sum = 0;

            foreach ([1, 3, 5, 10] as $top) {
                if (isset($item["days_top_$top"])) {
                    $sum += $item["days_top_$top"];
                }
            }

            $item->put($col, $sum);

            return $item;
        });
    }
}
