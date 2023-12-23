<?php

namespace App\Exports;

use App\Common;
use App\ProjectRelevanceHistory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class RelevanceStatisticsExport implements FromCollection
{

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        $history = ProjectRelevanceHistory::where('id', '=', $this->id)->first();

        $results = $history->stories()->get([
            'phrase', 'main_link', 'region',
            'last_check', 'points', 'position',
            'coverage', 'coverage_tf', 'density',
            'width', 'density', 'calculate',
            'id', 'project_relevance_history_id',
            'comment', 'user_id', 'state',
        ]);

        $rows[] = [
            'Ключевая фраза',
            'Посадочная страница',
            'Регион',
            'Позиция',

            'Баллы',
            'Рекомендованные баллы',

            'Охват важных слова',
            'Рекомендованное значение охвата важных слов',

            'Охват TF',
            'Рекомендованный охват TF',

            'Ширина',
            'Рекомендованная ширина',

            'Плотность',
            'Рекомендованная плотность',

            'Комментарий',
        ];

        foreach ($results as $result) {
            if (isset($result->results['average_values'])) {
                $avg = json_decode($result->results['average_values'], true);
            } else {
                $avg = [];
            }

            $rows[] = [
                'phrase' => (string)$result->phrase,
                'main_link' => (string)$result->main_link,

                'region' => Common::getRegionName($result->region),
                'position' => $result->position === 0 ? "Сайт не попал в топ" : $result->position,

                'points' => (int)round($result->points),
                'ideal_points' => isset($avg['points']) ? (int)round($avg['points']) : 'нет данных',

                'coverage' => (int)round($result->coverage),
                'ideal_coverage' => isset($avg['coverage']) ? (int)round($avg['coverage']) : 'нет данных',

                'coverage_tf' => (int)round($result->coverage_tf),
                'ideal_coverage_tf' => isset($avg['coverageTf']) ? (int)round($avg['coverageTf']) : 'нет данных',

                'width' => (int)round($result->width),
                'ideal_width' => isset($avg['width']) ? (int)round($avg['width']) : 'нет данных',

                'density' => (int)round($result->density),
                'ideal_density' => isset($avg['densityPercent']) ? (int)round($avg['densityPercent']) : 'нет данных',

                'comment' => $result->comment,
            ];

            //Удаляем лишнюю информацию из буфера, т.к данных очень много
            unset($result->results);
        }

        return collect($rows);
    }
}
