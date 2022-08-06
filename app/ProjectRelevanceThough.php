<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectRelevanceThough extends Model
{
    protected $table = 'project_relevance_though';

    protected $guarded = [];

    /**
     * @param $result
     * @param $mainId
     * @return mixed
     */
    public static function saveNewRecord($result, $mainId)
    {
        return ProjectRelevanceThough::firstOrCreate([
            'result' => base64_encode(gzcompress($result, 9)),
            'project_relevance_history_id' => $mainId
        ]);
    }

    /**
     * @param $items
     * @param $id
     * @param $countRecords
     * @return array
     */
    public static function thoughAnalyse($items, $id, $countRecords): array
    {
        $resultArray = [];

        foreach ($items as $item) {
            $record = RelevanceHistory::where('main_link', '=', $item->main_link)
                ->where('project_relevance_history_id', '=', $id)
                ->where('phrase', '=', $item->phrase)
                ->where('region', '=', $item->region)
                ->where('calculate', '=', 1)
                ->latest('last_check')
                ->with('results')
                ->with('mainHistory')
                ->first();

            if (isset($record) && $record->results->cleaning == 0) {
                foreach (json_decode(gzuncompress(base64_decode($record->results->unigram_table)), true) as $word) {
                    foreach ($word as $key => $item) {
                        if ($key != 'total') {
                            $words[$key] = $item;
                        }
                    }
                }
                foreach ($words as $key => $word) {
                    arsort($word['occurrences']);

                    if (isset($resultArray[$key])) {
                        $resultArray[$key]['tf'] += $word['tf'];
                        $resultArray[$key]['idf'] += $word['idf'];
                        $resultArray[$key]['repeatInLinkMainPage'] += $word['repeatInLinkMainPage'];
                        $resultArray[$key]['repeatInTextMainPage'] += $word['repeatInTextMainPage'];
                        $resultArray[$key]['throughLinks'] = array_merge($resultArray[$key]['throughLinks'], $word['occurrences']);
                        $resultArray[$key]['throughCount'] += 1;
                    } else {
                        $resultArray[$key]['tf'] = $word['tf'];
                        $resultArray[$key]['idf'] = $word['idf'];
                        $resultArray[$key]['repeatInLinkMainPage'] = $word['repeatInLinkMainPage'];
                        $resultArray[$key]['repeatInTextMainPage'] = $word['repeatInTextMainPage'];
                        $resultArray[$key]['throughLinks'] = $word['occurrences'];
                        $resultArray[$key]['throughCount'] = 1;
                    }

                    $resultArray[$key]['total'] = $countRecords;
                }
            }
        }

        return $resultArray;
    }

    /**
     * @param $array
     * @return array
     */
    public static function searchWordWorms($array): array
    {
        $stemmer = new LinguaStem();
        $ignoredWords = [];
        $wordWorms = [];

        foreach ($array as $key1 => $elem1) {
            if (!in_array($key1, $ignoredWords)) {
                foreach ($array as $key2 => $elem2) {
                    if (!in_array($key2, $ignoredWords)) {
                        similar_text($key1, $key2, $percent);
                        if (
                            preg_match("/[А-я]/", $key1) &&
                            $stemmer->getRootWord($key2) == $stemmer->getRootWord($key1) ||
                            preg_match("/[A-Za-z]/", $key1) &&
                            $percent >= 82
                        ) {
                            $wordWorms[$key1][$key2] = $elem2;
                            $ignoredWords[] = $key2;
                            $ignoredWords[] = $key1;
                        }
                    }
                }
            }
        }

        return $wordWorms;
    }

    /**
     * @param $wordWorms
     * @param $countRecords
     * @return array
     */
    public static function calculateFinalResult($wordWorms, $countRecords): array
    {
        foreach ($wordWorms as $key => $wordWorm) {
            $tf = $idf = $link = $text = $thoughCount = 0;
            $thoughLinks = [];
            foreach ($wordWorm as $items) {
                $thoughCount = max($items['total'], 0);
                $tf += $items['tf'];
                $idf += $items['idf'];
                $link += $items['repeatInLinkMainPage'];
                $text += $items['repeatInTextMainPage'];
                $thoughLinks = array_merge($items['throughLinks'], $thoughLinks);
            }
            $wordWorms[$key]['total'] = [
                'repeat' => $countRecords,
                'tf' => $tf,
                'idf' => $idf,
                'repeatInLinkMainPage' => $link,
                'repeatInTextMainPage' => $text,
                'throughLinks' => $thoughLinks,
                'throughCount' => $thoughCount,
            ];
        }

        return array_slice($wordWorms, 0, 2000);
    }
}
