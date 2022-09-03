<?php

namespace App\Exports;

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

        $excelRows[] = [
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
            $avg = json_decode($result->results['average_values'], true);
            $excelRows[] = [
                'phrase' => (string)$result->phrase,
                'main_link' => (string)$result->main_link,

                'region' => $this->getRegionName($result->region),
                'position' => $result->position === 0 ? "Сайт не попал в топ" : $result->position,

                'points' => (int)round($result->points),
                'ideal_points' => $avg['points'] ?? 'нет данных',

                'coverage' => (int)round($result->coverage),
                'ideal_coverage' => $avg['coverage'] ?? 'нет данных',

                'coverage_tf' => (int)round($result->coverage_tf),
                'ideal_coverage_tf' => $avg['coverageTf'] ?? 'нет данных',

                'width' => (int)round($result->width),
                'ideal_width' => $avg['width'] ?? 'нет данных',

                'density' => (int)round($result->density),
                'ideal_density' => $avg['densityPercent'] ?? 'нет данных',

                'comment' => $result->comment,
            ];

            //Удаляем лишнюю информацию из буфера, т.к данных очень много
            unset($result->results);
        }

        return collect($excelRows);
    }

    /**
     * @param string $id
     * @return string
     */
    protected function getRegionName(string $id): string
    {
        switch ($id) {
            case '1' :
                return __('Moscow');
            case '20' :
                return __('Arkhangelsk');
            case '37' :
                return __('Astrakhan');
            case '197' :
                return __('Barnaul');
            case '4' :
                return __('Belgorod');
            case '77' :
                return __('Blagoveshchensk');
            case '191' :
                return __('Bryansk');
            case '24' :
                return __('Veliky Novgorod');
            case '75' :
                return __('Vladivostok');
            case '33' :
                return __('Vladikavkaz');
            case '192' :
                return __('Vladimir');
            case '38' :
                return __('Volgograd');
            case '21' :
                return __('Vologda');
            case '193' :
                return __('Voronezh');
            case '1106' :
                return __('Grozny');
            case '54' :
                return __('Ekaterinburg');
            case '5' :
                return __('Ivanovo');
            case '63' :
                return __('Irkutsk');
            case '41' :
                return __('Yoshkar-ola');
            case '43' :
                return __('Kazan');
            case '22' :
                return __('Kaliningrad');
            case '64' :
                return __('Kemerovo');
            case '7' :
                return __('Kostroma');
            case '35' :
                return __('Krasnodar');
            case '62' :
                return __('Krasnoyarsk');
            case '53' :
                return __('Kurgan');
            case '8' :
                return __('Kursk');
            case '9' :
                return __('Lipetsk');
            case '28' :
                return __('Makhachkala');
            case '213' :
                return __('Moscow');
            case '23' :
                return __('Murmansk');
            case '1092' :
                return __('Nazran');
            case '30' :
                return __('Nalchik');
            case '47' :
                return __('Nizhniy Novgorod');
            case '65' :
                return __('Novosibirsk');
            case '66' :
                return __('Omsk');
            case '10' :
                return __('Eagle');
            case '48' :
                return __('Orenburg');
            case '49' :
                return __('Penza');
            case '50' :
                return __('Perm');
            case '25' :
                return __('Pskov');
            case '39' :
                return __('Rostov-on');
            case '11' :
                return __('Ryazan');
            case '51' :
                return __('Samara');
            case '42' :
                return __('Saransk');
            case '2' :
                return __('Saint-Petersburg');
            case '12' :
                return __('Smolensk');
            case '239' :
                return __('Sochi');
            case '36' :
                return __('Stavropol');
            case '973' :
                return __('Surgut');
            case '13' :
                return __('Tambov');
            case '14' :
                return __('Tver');
            case '67' :
                return __('Tomsk');
            case '15' :
                return __('Tula');
            case '195' :
                return __('Ulyanovsk');
            case '172' :
                return __('Ufa');
            case '76' :
                return __('Khabarovsk');
            case '45' :
                return __('Cheboksary');
            case '56' :
                return __('Chelyabinsk');
            case '1104' :
                return __('Cherkessk');
            case '16' :
                return __('Yaroslavl');
            default:
                return 'Регион не опознан';
        }
    }
}
