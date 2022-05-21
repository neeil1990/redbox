<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelevanceHistoryResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relevance_history_result', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('clouds_competitors');
            $table->longText('clouds_main_page');
            $table->longText('avg');
            $table->longText('main_page');
            $table->longText('unigram_table');
            $table->longText('sites');
            $table->longText('tf_comp_clouds');
            $table->longText('phrases');
            $table->longText('avg_coverage_percent');
            $table->longText('recommendations');
            $table->unsignedBigInteger('project_id');
            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')->on('relevance_history')
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
        Schema::dropIfExists('relevance_history_result');
    }
}
