<?php


namespace App\Classes\Cron;

use App\Models\Relevance\RelevanceAnalysisConfig;
use App\Models\Relevance\RelevanceHistoryResult;
use Carbon\Carbon;


class RelevanceCleaningResults
{
    public function __invoke()
    {
        $this->compressedAndCleaning();
        $this->compressed();
    }

    /**
     * @return void
     */
    public function compressedAndCleaning()
    {
        $total = 0;
        $config = RelevanceAnalysisConfig::first();

        $results = RelevanceHistoryResult::where([
            ['created_at', '<', Carbon::now()->subDays($config->cleaning_interval)],
            ['cleaning', '=', 0]
        ])->take(5)->get();

        while (count($results) != 0) {
            foreach ($results as $result) {
                $result->clouds_competitors =
                $result->clouds_main_page =
                $result->avg =
                $result->main_page =
                $result->unigram_table =
                $result->tf_comp_clouds =
                $result->phrases =
                $result->recommendations = '';

                if (!$result->compressed) {
                    $result->sites = base64_encode(gzcompress($result->sites, 9));
                    $result->avg_coverage_percent = base64_encode(gzcompress($result->avg_coverage_percent, 9));
                    $result->compressed = 1;
                }

                $result->cleaning = 1;
                $result->save();
            }

            $total += count($results);
            $results = RelevanceHistoryResult::where([
                ['created_at', '<', Carbon::now()->subDays($config->cleaning_interval)],
                ['cleaning', '=', 0]
            ])->take(5)->get();
        }
    }

    /**
     * @return void
     */
    public function compressed()
    {
        $total = 0;
        $config = RelevanceAnalysisConfig::first();

        $results = RelevanceHistoryResult::where([
            ['created_at', '<', Carbon::now()->subDays($config->cleaning_interval)],
            ['cleaning', '=', 1],
            ['compressed', '=', 0],
        ])->take(5)->get();

        while (count($results) != 0) {
            foreach ($results as $result) {
                $result->sites = base64_encode(gzcompress($result->sites, 9));
                $result->avg_coverage_percent = base64_encode(gzcompress($result->avg_coverage_percent, 9));
                $result->compressed = 1;
                $result->save();
            }

            $total += count($results);
            $results = RelevanceHistoryResult::where([
                ['created_at', '<', Carbon::now()->subDays($config->cleaning_interval)],
                ['cleaning', '=', 1],
                ['compressed', '=', 0],
            ])->take(5)->get();
        }
    }

}
