<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMonitoringGroupIdToMonitoringKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_keywords', function (Blueprint $table) {
            $table->bigInteger('monitoring_group_id')->unsigned()->nullable()->after('monitoring_project_id');

            $table->foreign('monitoring_group_id')
                ->references('id')
                ->on('monitoring_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitoring_keywords', function (Blueprint $table) {
            $table->dropColumn('monitoring_group_id');
        });
    }
}
