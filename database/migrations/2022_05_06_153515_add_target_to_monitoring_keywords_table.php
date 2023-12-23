<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTargetToMonitoringKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_keywords', function (Blueprint $table) {
            $table->integer('target')->unsigned()->nullable()->after('monitoring_project_id');
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
            $table->dropColumn('target');
        });
    }
}
