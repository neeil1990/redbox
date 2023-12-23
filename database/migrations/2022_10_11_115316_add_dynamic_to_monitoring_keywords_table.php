<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDynamicToMonitoringKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_keywords', function (Blueprint $table) {
            $table->integer('dynamic')->nullable()->after('page');
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
            $table->dropColumn('dynamic');
        });
    }
}
