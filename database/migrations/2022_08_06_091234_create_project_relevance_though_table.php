<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectRelevanceThoughTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_relevance_though', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('result');
            $table->unsignedBigInteger('project_relevance_history_id');

            $table->foreign('project_relevance_history_id')
                ->references('id')
                ->on('project_relevance_history')
                ->onDelete('cascade');
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
        Schema::dropIfExists('project_relevance_though');
    }
}
