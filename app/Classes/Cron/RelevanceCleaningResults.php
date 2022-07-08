<?php


namespace App\Classes\Cron;

use App\RelevanceAnalysisConfig;
use App\RelevanceHistoryResult;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class RelevanceCleaningResults
{
    public function __invoke()
    {
        Log::debug('Запущена отчистка');
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

            $results = RelevanceHistoryResult::where([
                ['created_at', '<', Carbon::now()->subDays(5)],
                ['cleaning', '=', 0]
            ])->take(5)->get();
        }
        Log::debug('Отчистка завершена');
    }

}
