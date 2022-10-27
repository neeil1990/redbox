<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutoupdateTimeWeekdaysMonthdayToMonitoringSearchenginesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_searchengines', function (Blueprint $table) {
            $table->boolean('auto_update')->default(false);
            $table->time('time')->nullable();
            $table->longText('weekdays')->nullable();
            $table->integer('monthday')->nullable();
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
            $table->dropColumn('auto_update');
            $table->dropColumn('time');
            $table->dropColumn('weekdays');
            $table->dropColumn('monthday');
        });
    }
}
