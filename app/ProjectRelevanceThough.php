<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

            try {
                if (isset($record) && isset($record->results) && $record->results->cleaning == 0) {
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
                            $resultArray[$key]['tf'] += round($word['tf'], 6);
                            $resultArray[$key]['idf'] += round($word['idf'], 6);
                            $resultArray[$key]['repeatInLinkMainPage'] += $word['repeatInLinkMainPage'];
                            $resultArray[$key]['repeatInTextMainPage'] += $word['repeatInTextMainPage'];
                            $resultArray[$key]['throughLinks'] = array_merge($resultArray[$key]['throughLinks'], $word['occurrences']);
                            $resultArray[$key]['throughCount'] += 1;
                        } else {
                            $resultArray[$key] = [
                                'tf' => round($word['tf'], 6),
                                'idf' => round($word['idf'], 6),
                                'repeatInLinkMainPage' => $word['repeatInLinkMainPage'],
                                'repeatInTextMainPage' => $word['repeatInTextMainPage'],
                                'throughLinks' => $word['occurrences'],
                                'throughCount' => 1,
                            ];
                        }

                        $resultArray[$key]['total'] = $countRecords;
                    }
                }
            } catch (\Exception $e) {
                Log::debug('though error', [
                    'record' => $record,
                    'cleaning' => $record->results ?? null,
                ]);
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
                $thoughCount += 1;
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
