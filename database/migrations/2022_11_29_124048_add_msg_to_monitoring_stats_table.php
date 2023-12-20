<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMsgToMonitoringStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_stats', function (Blueprint $table) {
            $table->longText('msg')->nullable()->after('errors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitoring_stats', function (Blueprint $table) {
            $table->dropColumn('msg');
        });
    }
}
