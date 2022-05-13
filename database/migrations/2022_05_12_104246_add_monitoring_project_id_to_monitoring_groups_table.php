<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMonitoringProjectIdToMonitoringGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_groups', function (Blueprint $table) {
            $table->bigInteger('monitoring_project_id')->unsigned()->after('id');

            $table->foreign('monitoring_project_id')
                ->references('id')
                ->on('monitoring_projects')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitoring_groups', function (Blueprint $table) {
            $table->dropForeign(['monitoring_project_id']);
            $table->dropColumn('monitoring_project_id');
        });
    }
}
