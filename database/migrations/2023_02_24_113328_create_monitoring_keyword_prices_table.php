<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitoringKeywordPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_keyword_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('monitoring_keyword_id')->unsigned();
            $table->bigInteger('monitoring_searchengine_id')->unsigned();
            $table->decimal('top1', 15, 2)->default('0.00');
            $table->decimal('top3', 15, 2)->default('0.00');
            $table->decimal('top5', 15, 2)->default('0.00');
            $table->decimal('top10', 15, 2)->default('0.00');
            $table->decimal('top20', 15, 2)->default('0.00');
            $table->decimal('top50', 15, 2)->default('0.00');
            $table->decimal('top100', 15, 2)->default('0.00');
            $table->timestamps();

            $table->foreign('monitoring_keyword_id')
                ->references('id')
                ->on('monitoring_keywords')
                ->onDelete('cascade');

            $table->foreign('monitoring_searchengine_id')
                ->references('id')
                ->on('monitoring_searchengines');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitoring_keyword_prices');
    }
}
