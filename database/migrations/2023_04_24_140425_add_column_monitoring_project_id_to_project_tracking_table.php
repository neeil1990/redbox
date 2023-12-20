<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMonitoringProjectIdToProjectTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_tracking', function (Blueprint $table) {
            $table->bigInteger('monitoring_project_id')->unsigned()->nullable()->after('user_id');

            $table->foreign('monitoring_project_id')
                ->references('id')
                ->on('monitoring_projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_tracking', function (Blueprint $table) {
            $table->dropColumn('monitoring_project_id');
        });
    }
}
