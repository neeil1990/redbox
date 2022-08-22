<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitoringStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue');
            $table->bigInteger('queue_id')->nullable();
            $table->string('model_class')->nullable();
            $table->bigInteger('model_id')->nullable();
            $table->boolean('errors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitoring_stats');
    }
}
