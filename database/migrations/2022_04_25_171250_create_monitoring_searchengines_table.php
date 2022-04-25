<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitoringSearchenginesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_searchengines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('monitoring_progect_id')->unsigned();
            $table->string('engine');
            $table->string('lr');
            $table->string('lang');
            $table->timestamps();

            $table->foreign('monitoring_progect_id')
                ->references('id')
                ->on('monitoring_progects')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitoring_searchengines');
    }
}
