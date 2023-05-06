<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitoringChangesDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_changes_date', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('monitoring_project_id');
            $table->string('range');
            $table->longText('result')->nullable();
            $table->string('state')->default('in queue');
            $table->longText('request');
            $table->timestamps();

            $table->foreign('monitoring_project_id')
                ->references('id')
                ->on('monitoring_projects')
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
        Schema::dropIfExists('monitoring_changes_date');
    }
}
