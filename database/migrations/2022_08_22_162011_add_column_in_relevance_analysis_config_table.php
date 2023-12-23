<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInRelevanceAnalysisConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relevance_analysis_config', function (Blueprint $table) {
            $table->string('word_worms')->default('phpmorphy');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('relevance_analysis_config', function (Blueprint $table) {
            $table->dropColumn('word_worms');
        });
    }
}
