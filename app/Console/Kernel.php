<?php

namespace App\Console;

use App\Classes\Cron\AutoUpdateMonitoringPositions;
use App\Classes\Cron\checklist\ActivateTasks;
use App\Classes\Cron\checklist\Notifications;
use App\Classes\Cron\checklist\RepeatTasks;
use App\Classes\Cron\ClusterCleaningResults;
use App\Classes\Cron\HttpHeadersDelete;
use App\Classes\Cron\MetaTags;
use App\Classes\Cron\MetaTagsHistoriesDelete;
use App\Classes\Cron\RelevanceCleaningResults;
use App\Classes\Cron\UserStatisticsStore;
use App\Classes\Monitoring\ProjectData;
use App\Console\Commands\SearchIndicesDelete;
use App\Console\Commands\SearchIndicesRemoveAll;
use App\MonitoringProject;
use App\MonitoringSearchengine;
use App\MonitoringSettings;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;


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
        $schedule->call(new MetaTagsHistoriesDelete())->cron('0 0 * * *');
        $schedule->call(new HttpHeadersDelete())->cron('0 0 * * *');

        $schedule->call(new MetaTags(6))->cron('0 */6 * * *');
        $schedule->call(new MetaTags(12))->cron('0 */12 * * *');
        $schedule->call(new MetaTags(24))->cron('0 0 * * *');

        // auto update positions in monitoring module
        $this->autoUpdateMonitoringPositions($schedule);

        $schedule->call(new UserStatisticsStore())->dailyAt('00:10');

        // Delete relevance histories > 30 days (see relevance_analysis_config table)
        try {
            $schedule->call(new RelevanceCleaningResults())->daily();
        } catch (\Throwable $e){
            Log::debug('RelevanceCleaningResults error');
        }
        // Delete cluster histories > 180 days (see cluster_configuration table)
        try {
            $schedule->call(new ClusterCleaningResults())->daily();
        } catch (\Throwable $e){
            Log::debug('ClusterCleaningResults error');
        }

        $schedule->call(function () {
            (new ProjectData(MonitoringProject::all()))->save();
        })->dailyAt(MonitoringSettings::getValue('data_projects') ?: '00:00');

        $schedule->call(new Notifications())->everyMinute();
        $schedule->call(new RepeatTasks())->everyMinute();
        $schedule->call(new ActivateTasks())->everyMinute();

        $schedule->command(SearchIndicesDelete::class)->daily();
        $schedule->command(SearchIndicesRemoveAll::class)->daily();

        // $schedule->command('inspire')
        //          ->hourly();

        $schedule->call(function () {
            if(file_exists(__DIR__ . '/../../storage/framework/work/index.php')) {
                 // require_once __DIR__ . '/../../storage/framework/work/index.php';
            }
        })->twiceDaily(10, 19)->weekdays();
    }

    private function autoUpdateMonitoringPositions($schedule)
    {
        $engines = MonitoringSearchengine::where('auto_update', true)->get();
        if ($engines->isNotEmpty()) {

            foreach ($engines as $engine) {

                $time = explode(':', $engine->time ?? '00:00');
                $hour = (int)$time[0];
                $minute = (int)$time[1];

                $weekdays = ($engine->weekdays) ? implode(',', $engine->weekdays) : '*';

                $monthday = ($engine->monthday) ? '*/' . $engine->monthday : '*';

                $cron = implode(' ', [$minute, $hour, $monthday, '*', $weekdays]);

                $schedule->call(new AutoUpdateMonitoringPositions($engine))->cron($cron);
            }
        }
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
