<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangedWaitingTimeInDomainMonitoringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $projects = \App\DomainMonitoring::all();
        if (isset($projects)) {
            foreach ($projects as $project) {
                $project->waiting_time = 10;
                $project->save();
            }
        }
    }
}
