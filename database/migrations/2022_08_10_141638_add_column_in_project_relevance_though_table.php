<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInProjectRelevanceThoughTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_relevance_though', function (Blueprint $table) {
            $table->longText('though_words')->nullable();
            $table->longText('word_worms')->nullable();
            $table->integer('state')->nullable();
            $table->integer('stage')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_relevance_though', function (Blueprint $table) {
            $table->dropColumn('though_words');
            $table->dropColumn('word_worms');
            $table->dropColumn('state');
            $table->dropColumn('stage');
        });
    }
}
