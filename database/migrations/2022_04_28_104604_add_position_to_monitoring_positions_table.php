<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPositionToMonitoringPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_positions', function (Blueprint $table) {
            $table->integer('position')->unsigned()->nullable()->after('monitoring_searchengine_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitoring_positions', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
}
