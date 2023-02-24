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
            $table->string('top1')->nullable();
            $table->string('top3')->nullable();
            $table->string('top5')->nullable();
            $table->string('top10')->nullable();
            $table->string('top20')->nullable();
            $table->string('top50')->nullable();
            $table->string('top100')->nullable();
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
