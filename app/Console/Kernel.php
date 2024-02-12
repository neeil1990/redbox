<?php

namespace App\Console;

use App\Classes\Cron\AutoUpdateMonitoringPositions;
use App\Classes\Cron\checklist\CheckListCheckStatus;
use App\Classes\Cron\checklist\CheckListNotifications;
use App\Classes\Cron\ClusterCleaningResults;
use App\Classes\Cron\MetaTags;
use App\Classes\Cron\MetaTagsHistoriesDelete;
use App\Classes\Cron\RelevanceCleaningResults;
use App\Classes\Cron\UserMonitoringProjectSave;
use App\Classes\Monitoring\ProjectDataTableUpdateDB;
use App\MonitoringProject;
use App\MonitoringSearchengine;
use App\MonitoringSettings;
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

        $schedule->call(new MetaTags(6))->cron('0 */6 * * *');
        $schedule->call(new MetaTags(12))->cron('0 */12 * * *');
        $schedule->call(new MetaTags(24))->cron('0 0 * * *');

        // Delete relevance histories > 30 days (see relevance_analysis_config table)
        $schedule->call(new RelevanceCleaningResults())->daily();

        // Delete cluster histories > 180 days (see cluster_configuration table)
        $schedule->call(new ClusterCleaningResults())->daily();

        // auto update positions in monitoring module
        $this->autoUpdateMonitoringPositions($schedule);

        $schedule->call(function () {
            (new ProjectDataTableUpdateDB(MonitoringProject::all()))->save();
        })->dailyAt(MonitoringSettings::getValue('data_projects') ?: '00:00');

        $schedule->call(new CheckListNotifications())->everyMinute();

        $schedule->call(new UserMonitoringProjectSave())->monthlyOn(UserMonitoringProjectSave::DAY, UserMonitoringProjectSave::TIME);

        // $schedule->command('inspire')
        //          ->hourly();
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
