<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChecklistRelationWithMonitoringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_relation_with_monitoring', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('checklist_id');
            $table->unsignedBigInteger('monitoring_id');

            $table->foreign('checklist_id')
                ->references('id')
                ->on('checklist_projects')
                ->onDelete('cascade');

            $table->foreign('monitoring_id')
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
        Schema::dropIfExists('checklist_relation_with_monitoring');
    }
}
