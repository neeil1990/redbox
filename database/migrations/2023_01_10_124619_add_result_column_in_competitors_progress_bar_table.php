<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResultColumnInCompetitorsProgressBarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competitors_progress_bar', function (Blueprint $table) {
            $table->longText('result')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competitors_progress_bar', function (Blueprint $table) {
            $table->dropColumn('result');
        });
    }
}
