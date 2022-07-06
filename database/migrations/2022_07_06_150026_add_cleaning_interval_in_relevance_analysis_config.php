<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCleaningIntervalInRelevanceAnalysisConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relevance_analysis_config', function (Blueprint $table) {
            $table->integer('cleaning_interval')->default(30);
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
            $table->dropColumn('cleaning_interval');
        });
    }
}
