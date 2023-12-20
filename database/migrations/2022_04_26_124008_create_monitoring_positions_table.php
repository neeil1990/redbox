<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitoringPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_positions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('monitoring_keyword_id')->unsigned();
            $table->bigInteger('monitoring_searchengine_id')->unsigned();
            $table->integer('position')->unsigned();
            $table->integer('target')->unsigned()->nullable();
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
        Schema::dropIfExists('monitoring_positions');
    }
}
