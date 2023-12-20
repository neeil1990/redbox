<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelevanceHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relevance_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('phrase');
            $table->string('region');
            $table->string('main_link');
            $table->string('last_check');
            $table->integer('points');
            $table->integer('position');
            $table->integer('coverage');
            $table->integer('coverage_tf');
            $table->integer('width');
            $table->integer('density');
            $table->boolean('calculate')->default(true);
            $table->timestamps();

            $table->unsignedBigInteger('project_relevance_history_id');
            $table->foreign('project_relevance_history_id')
                ->references('id')
                ->on('project_relevance_history')
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
        Schema::dropIfExists('relevance_history');
    }
}
