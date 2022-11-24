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
        $file[] = [__('Sequence number'), __('Sequence number in the cluster'), __('Key query'),__('Group'), __('Relevant Page'), __('Base'), __('Phrasal'), __('Target')];
        $results = json_decode(gzuncompress(base64_decode($this->cluster->result)), true);
        $clusterIterator = 1;
        $iterator = 1;
        foreach ($results as $items) {
            foreach ($items as $phrase => $item) {
                if ($phrase !== 'finallyResult') {
                    if (isset($item['relevance'])) {
                        $relevance = $item['relevance'][0];
                    } elseif (isset($item['link'])) {
                        $relevance = $item['link'];
                    } else {
                        $relevance = '';
                    }
                    $file[] = [
                        $clusterIterator,
                        $iterator,
                        $phrase,
                        $items['finallyResult']['groupName'],
                        $relevance,
                        $item['based']['number'] ?? 0,
                        isset($item['phrased']) ? $item['phrased']['number'] : 0,
                        isset($item['target']) ? $item['target']['number'] : 0,
                    ];
                    $iterator++;
                }
            }
            $clusterIterator++;
        }

        return collect($file);
    }
}
