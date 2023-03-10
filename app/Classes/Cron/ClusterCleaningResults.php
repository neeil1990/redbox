<?php

namespace App\Classes\Cron;

use App\ClusterConfiguration;
use App\ClusterResults;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class ClusterCleaningResults
{
    public function __invoke()
    {
        $this->cleaning();
    }

    public function cleaning()
    {
        Log::debug('daily check days old ClusterCleaningResults');

        ClusterResults::where('created_at', '<', Carbon::now()->subDays(ClusterConfiguration::first('cleaning_interval')->cleaning_interval))->delete();
    }
}
