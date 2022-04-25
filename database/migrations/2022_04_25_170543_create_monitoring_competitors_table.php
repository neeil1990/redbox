<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitoringCompetitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_competitors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('monitoring_project_id')->unsigned();
            $table->string('url')->nullable();
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
        Schema::dropIfExists('monitoring_competitors');
    }
}
