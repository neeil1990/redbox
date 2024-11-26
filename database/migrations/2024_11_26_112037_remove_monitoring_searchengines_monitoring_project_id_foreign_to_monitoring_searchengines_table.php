<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveMonitoringSearchenginesMonitoringProjectIdForeignToMonitoringSearchenginesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_searchengines', function (Blueprint $table) {
            $table->dropForeign(['monitoring_project_id']);
            $table->dropIndex('monitoring_searchengines_monitoring_project_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitoring_searchengines', function (Blueprint $table) {
            $table->foreign('monitoring_project_id')
                ->references('id')
                ->on('monitoring_projects')
                ->onDelete('cascade');
        });
    }
}
