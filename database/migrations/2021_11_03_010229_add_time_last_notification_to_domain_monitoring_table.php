<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeLastNotificationToDomainMonitoringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domain_monitoring', function (Blueprint $table) {
            $table->string('time_last_notification')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domain_monitoring', function (Blueprint $table) {
            $table->dropColumn('time_last_notification');
        });
    }
}
