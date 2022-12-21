<?php

namespace App\Exports;

use App\Models\Cluster\ClusterResults;
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
        $file[] = [__('Sequence number'), __('Sequence number in the cluster'), __('Key query'), __('Group'), __('Relevant Page'), __('Base'), __('Phrasal'), __('Target')];
        $results = json_decode(gzuncompress(base64_decode($this->cluster->result)), true);
        $clusterIterator = 1;
        $iterator = 1;
        foreach ($results as $items) {
            foreach ($items as $phrase => $item) {
                $relevance = '';
                if ($phrase !== 'finallyResult') {
                    if (isset($item['relevance']) && is_array($item['relevance'])) {
                        $relevance = $item['relevance'][0];
                    } elseif (isset($item['link'])) {
                        $relevance = $item['link'];
                    }

                    $baseForm = '0';
                    if (isset($item['based']['number'])) {
                        $baseForm = $item['based']['number'];
                    }

                    $phraseForm = '0';
                    if (isset($item['phrased']['number'])) {
                        $phraseForm = $item['phrased']['number'];
                    }

                    $targetForm = '0';
                    if (isset($item['target']['number'])) {
                        $targetForm = $item['target']['number'];
                    }

                    $file[] = [
                        $clusterIterator,
                        $iterator,
                        $phrase,
                        $items['finallyResult']['groupName'] ?? '',
                        $relevance,
                        $baseForm,
                        $phraseForm,
                        $targetForm,
                    ];
                    $iterator++;
                }
            }
            $clusterIterator++;
        }

        return collect($file);
    }

}
