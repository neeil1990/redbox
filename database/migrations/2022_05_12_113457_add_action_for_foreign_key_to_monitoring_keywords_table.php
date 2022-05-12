<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActionForForeignKeyToMonitoringKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_keywords', function (Blueprint $table) {
            $table->dropIndex('monitoring_keywords_monitoring_group_id_foreign');

            $table->foreign('monitoring_group_id')
                ->references('id')
                ->on('monitoring_groups')
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
        Schema::table('monitoring_keywords', function (Blueprint $table) {
            $table->dropForeign(['monitoring_group_id']);
        });
    }
}
