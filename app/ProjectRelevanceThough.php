<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ProjectRelevanceThough extends Model
{
    protected $table = 'project_relevance_though';

    protected $guarded = [];

    /**
     * @param $items
     * @param $id
     * @param $countRecords
     * @return void
     */
    public static function thoughAnalyse($items, $id, $countRecords)
    {
        $resultArray = [];
        $cleaningProjects = [];

        foreach ($items as $item) {
            $record = RelevanceHistory::where('main_link', '=', $item['main_link'])
                ->where('project_relevance_history_id', '=', $id)
                ->where('phrase', '=', $item['phrase'])
                ->where('region', '=', $item['region'])
                ->where('calculate', '=', 1)
                ->latest('last_check')
                ->with('results')
                ->with('mainHistory')
                ->first();

            if (isset($record) && isset($record->results) && $record->results->cleaning == 0) {
                $words = [];

                foreach (json_decode(gzuncompress(base64_decode($record->results->unigram_table)), true) as $word) {
                    unset($word['total']);
                    foreach ($word as $key => $item) {
                        $key = trim(str_replace(chr(194) . chr(160), ' ', html_entity_decode($key)));
                        if ($key != '') {
                            $words[$key] = $item;
                        }
                    }
                }

                foreach ($words as $key => $word) {
                    arsort($word['occurrences']);

                    if (isset($resultArray[$key])) {
                        $resultArray[$key]['tf'] += round($word['tf'], 6);
                        $resultArray[$key]['idf'] += round($word['idf'], 6);
                        $resultArray[$key]['repeatInLinkMainPage'] += $word['repeatInLinkMainPage'];
                        $resultArray[$key]['repeatInTextMainPage'] += $word['repeatInTextMainPage'];
                        $resultArray[$key]['throughLinks'] = array_merge($resultArray[$key]['throughLinks'], $word['occurrences']);
                        $resultArray[$key]['repeatInLink'] += $word['avgInLink'];
                        $resultArray[$key]['repeatInText'] += $word['avgInText'];
                        $resultArray[$key]['throughCount'] += 1;
                    } else {
                        $resultArray[$key] = [
                            'tf' => round($word['tf'], 6),
                            'idf' => round($word['idf'], 6),
                            'repeatInLinkMainPage' => $word['repeatInLinkMainPage'],
                            'repeatInTextMainPage' => $word['repeatInTextMainPage'],
                            'throughLinks' => $word['occurrences'],
                            'repeatInLink' => $word['avgInLink'],
                            'repeatInText' => $word['avgInText'],
                            'throughCount' => 1,
                        ];
                    }
                }

            } else {
                if (isset($record->results->project_id)) {
                    $cleaningProjects[] = $record->results->project_id;
                }
                $countRecords--;
            }

            foreach ($resultArray as $key => $word) {
                $resultArray[$key]['total'] = $countRecords;
            }
        }

        $though = ProjectRelevanceThough::firstOrNew([
            'project_relevance_history_id' => $id,
        ]);

        $though->though_words = base64_encode(gzcompress(json_encode(array_slice($resultArray, 0, 5000)), 9));
        $though->stage = 2;
        $though->cleaning_projects = json_encode($cleaningProjects);
        $though->cleaning_state = 0;
        $though->save();
    }

    /**
     * @param $array
     * @param $mainId
     * @return void
     */
    public static function searchWordWorms($array, $mainId)
    {
        $ignoredWords = [];
        $wordWorms = [];

        foreach ($array as $key1 => $elem1) {
            if (!in_array($key1, $ignoredWords)) {
                foreach ($array as $key2 => $elem2) {
                    if (!in_array($key2, $ignoredWords)) {
                        similar_text($key1, $key2, $percent);
                        if ($percent < 82) {
                            continue 2;
                        } else {
                            $wordWorms[$key1][$key2] = $elem2;
                            $ignoredWords[] = $key2;
                            $ignoredWords[] = $key1;
                        }
                    }
                }
            }
        }

        $though = ProjectRelevanceThough::firstOrNew([
            'project_relevance_history_id' => $mainId,
        ]);

        $though->though_words = '';
        $though->word_worms = base64_encode(gzcompress(json_encode($wordWorms), 9));
        $though->stage = 3;
        $though->save();
    }

    /**
     * @param $wordWorms
     * @param $mainId
     * @return void
     */
    public static function calculateFinalResult($wordWorms, $mainId)
    {
        $wordWorms = collect($wordWorms)->sortBy('tf')->toArray();

        $though = ProjectRelevanceThough::firstOrNew([
            'project_relevance_history_id' => $mainId,
        ]);

        $though->result = base64_encode(gzcompress(json_encode(array_slice($wordWorms, 0, 3500)), 9));
        $though->word_worms = '';
        $though->state = 1;
        $though->save();
    }
}
