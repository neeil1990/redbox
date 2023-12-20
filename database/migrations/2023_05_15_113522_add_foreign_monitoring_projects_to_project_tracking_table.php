<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignMonitoringProjectsToProjectTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_tracking', function (Blueprint $table) {
            $table->dropForeign(['monitoring_project_id']);

            $table->foreign('monitoring_project_id')
                ->references('id')
                ->on('monitoring_projects')
                ->onDelete('SET NULL');
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
            $table->dropForeign(['monitoring_project_id']);

            $table->foreign('monitoring_project_id')
                ->references('id')
                ->on('monitoring_projects');
        });
    }
}
