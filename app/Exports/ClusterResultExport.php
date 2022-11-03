<?php

namespace App\Exports;

use App\ClusterResults;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ClusterResultExport implements FromCollection
{
    protected $cluster;

    public function __construct(ClusterResults $cluster)
    {
        $this->cluster = $cluster;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        $file[] = ['Порядковый номер', 'Порядковый номер в кластере', 'Ключевой запрос', 'Группа', 'Базовая', 'Фразовая', '"!Точная"'];
        $results = json_decode(gzuncompress(base64_decode($this->cluster->result)), true);
        $clusterIterator = 1;
        $iterator = 1;
        foreach ($results as $items) {
            foreach ($items as $phrase => $item) {
                if ($phrase !== 'finallyResult') {
                    $file[] = [
                        $clusterIterator,
                        $iterator,
                        $phrase,
                        $items['finallyResult']['groupName'],
                        $item['based']['number'],
                        isset($item['phrased']) ? $item['phrased']['number'] : 'нет данных',
                        isset($item['target']) ? $item['target']['number'] : 'нет данных',
                    ];
                    $iterator++;
                }
            }
            $clusterIterator++;
        }

        return collect($file);
    }
}
