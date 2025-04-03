<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDayToMonitoringSearchenginesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_searchengines', function (Blueprint $table) {
            $table->integer('day')->nullable()->after('monthday');
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
            $table->dropColumn('day');
        });
    }
}
