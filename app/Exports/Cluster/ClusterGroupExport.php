<?php

namespace App\Exports\Cluster;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ClusterGroupExport implements FromCollection
{
    protected array $clusters;

    protected array $array;

    protected int $nestCounter;

    protected int $chapter;

    protected $file;

    protected int $maxNested;

    protected int $clusterNumber = 1;

    public function __construct($clusters, $array)
    {
        $this->maxNested = 0;
        $this->chapter = 0;
        $this->clusters = $clusters;
        $this->array = $array;
        $this->nestCounter = 0;
    }

    public function collection(): Collection
    {
        return $this->loopConfirmation();
    }

    public function loopConfirmation($setMaxNested = true): Collection
    {
        if ($setMaxNested) {
            $this->confirmation();
        } else {
            $this->file = [];
        }

        $this->file[] = [
            __('Sequence number'),
            __('Sequence number in the cluster'),
            __('Chapter'),
        ];

        for ($i = 1; $i <= $this->maxNested; $i++) {
            $this->file[0][] = __('Subsection') . " $i";
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
            $this->loopArray($items, $groupName);
            $this->clusterNumber++;
        }

        return $this->checkExtraColumns();
    }

    public function confirmation()
    {
        foreach ($this->array as $mainPhrase => $items) {
            foreach ($items as $offPhrase => $item) {
                if (is_array($item)) {
                    $nestCounter = 0;
                    $this->array[$mainPhrase][array_key_first($item)] = $this->loop($item[array_key_first($item)], ++$nestCounter);
                } else {
                    $this->array[$mainPhrase][$item] = $this->setValues($item);
                }

                unset($this->array[$mainPhrase][$offPhrase]);
            }

            if ($this->maxNested < $this->nestCounter) {
                $this->maxNested = $this->nestCounter;
            }
            $this->nestCounter = 0;
        }
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

    public function loop($elems, $counter): array
    {
        $this->nestCounter = $counter;

        $res = [];
        foreach ($elems as $offPhrase => $item) {
            if (is_array($item)) {
                $res[array_key_first($item)] = $this->loop($item[array_key_first($item)], $counter + 1);
            } else {
                $res[$item] = $this->setValues($item);
            }
        }

        return $res;
    }

    public function loopArray($items, $groupName)
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
                $this->loopArray($info, $phrase);
            }
        }
        if ($this->chapter > 0) {
            $this->chapter--;
        }
    }

    public function checkExtraColumns(): Collection
    {
        $columns = [];

        foreach ($this->file as $item) {
            $columns[] = count($item);
        }

        $max = max(array_keys(array_count_values($columns))) - 8;
        if ($this->maxNested !== $max) {
            $this->maxNested = $max;

            $this->file = [];
            return $this->loopConfirmation(false);
        }

        return collect($this->file);
    }
}
