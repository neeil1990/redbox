<?php

namespace App\Exports\Cluster;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ClusterGroupExport implements FromCollection
{
    protected $clusters;

    protected $array;

    protected $nestCounter = 0;

    protected $chapter = 0;

    protected $file;

    protected $maxNested;

    protected $clusterNumber = 1;

    public function __construct($clusters, $array)
    {
        $this->clusters = $clusters;
        $this->array = $array;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        $iterator = 1;
        $this->maxNested = $this->confirmation();

        $this->file[] = [
            __('Sequence number'),
            __('Sequence number in the cluster'),
            __('Chapter'),
        ];

        for ($i = 1; $i <= $this->maxNested; $i++) {
            $this->file[0][] = 'Подраздел ' . $i;
        }

        $this->file[0] = array_merge($this->file[0], [
            __('Key query'),
            __('Relevant'),
            __('Base'),
            __('Phrasal'),
            __('Accurate')
        ]);

        foreach ($this->array as $groupName => $items) {
            $this->chapter = 0;
            $this->loopArray($items, $iterator, $groupName);
            $this->clusterNumber++;
        }

        return collect($this->file);
    }

    public function confirmation()
    {
        $nestCounter = 0;
        foreach ($this->array as $mainPhrase => $items) {
            foreach ($items as $offPhrase => $item) {
                if (is_array($item)) {
                    $this->array[$mainPhrase][array_key_first($item)] = $this->loop($item[array_key_first($item)]);
                } else {
                    $this->array[$mainPhrase][$item] = $this->setValues($item);
                }

                unset($this->array[$mainPhrase][$offPhrase]);
            }
            if ($nestCounter < $this->nestCounter) {
                $nestCounter = $this->nestCounter;
            }
            $this->nestCounter = 0;
        }


        return $nestCounter;
    }

    public function setValues($offPhrase): array
    {
        foreach ($this->clusters as $cluster) {
            if (array_key_exists($offPhrase, $cluster)) {
                return $cluster[$offPhrase];
            }
        }

        return [];
    }

    public function loop($elems): array
    {
        $this->nestCounter += 1;
        $res = [];
        foreach ($elems as $offPhrase => $item) {
            if (is_array($item)) {
                $res[array_key_first($item)] = $this->loop($item[array_key_first($item)]);
            } else {
                $res[$item] = $this->setValues($item);
            }
        }

        return $res;
    }

    public function loopArray($items, $iterator, $groupName)
    {
        foreach ($items as $phrase => $info) {
            if (array_key_exists('based', $info)) {
                $before = [];
                if ($this->chapter > 0) {
                    for ($i = 0; $i < $this->chapter; $i++) {
                        $before[] = '';
                    }
                }

                $after = [];
                for ($i = 0; $i < $this->maxNested - $this->chapter; $i++) {
                    $after[] = '';
                }

                if (isset($info['link'])) {
                    $relevance = $info['link'];
                } else if (is_array($info['relevance'])) {
                    $relevance = $info['relevance'][0];
                } else {
                    $relevance = '';
                }

                if (is_array($info['based'])) {
                    $base = (string)$info['based']['number'];
                } else {
                    $base = '0';
                }

                if (is_array($info['phrased'])) {
                    $phrased = (string)$info['phrased']['number'];
                } else {
                    $phrased = '0';
                }

                if (is_array($info['target'])) {
                    $target = (string)$info['target']['number'];
                } else {
                    $target = '0';
                }

                $this->file[] = array_merge([$this->clusterNumber, count($this->file)], $before, [$groupName], $after, [$phrase, $relevance, $base, $phrased, $target]);
            } else {
                $this->chapter++;
                $this->clusterNumber++;
                $this->loopArray($info, $iterator, $phrase);
            }
        }
        if ($this->chapter > 0) {
            $this->chapter--;
        }
    }
}
