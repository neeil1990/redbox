<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignToMonitoringKeywordPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_keyword_prices', function (Blueprint $table) {
            $table->dropForeign(['monitoring_searchengine_id']);
            $table->dropIndex('monitoring_keyword_prices_monitoring_searchengine_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitoring_keyword_prices', function (Blueprint $table) {
            $table->foreign('monitoring_searchengine_id')
                ->references('id')
                ->on('monitoring_searchengines');
        });
    }
}
