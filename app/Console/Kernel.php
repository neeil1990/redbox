<?php

namespace App\Console;

use App\Classes\Cron\MetaTags;
use App\Classes\Cron\MetaTagsHistoriesDelete;
use App\Classes\Cron\RelevanceCleaningResults;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Delete histories > 90 days
        $schedule->call(new MetaTagsHistoriesDelete())->cron('0 0 * * *');

        //test call as 6
        //$schedule->call(new MetaTags(6))->cron('* * * * *');

        $schedule->call(new MetaTags(6))->cron('0 */6 * * *');
        $schedule->call(new MetaTags(12))->cron('0 */12 * * *');
        $schedule->call(new MetaTags(24))->cron('0 0 * * *');

        // Delete relevance histories > 30 (see relevance_analysis_config table) days
        $schedule->call(new RelevanceCleaningResults())->daily();

        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
