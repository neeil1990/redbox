<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectRelevanceHistoryTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_relevance_history_tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('relevance_history_id');
            $table->unsignedBigInteger('tags_id');

            $table->foreign('relevance_history_id')
                ->references('id')->on('project_relevance_history')
                ->onDelete('cascade');

            $table->foreign('tags_id')
                ->references('id')->on('relevance_tags')
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
        Schema::dropIfExists('project_relevance_history_tags');
    }
}
