<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAvgPositionColumnInProjectRelevanceHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_relevance_history', function (Blueprint $table) {
            $table->float('avg_position')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('project_relevance_history', 'avg_position')) {
            Schema::table('project_relevance_history', function (Blueprint $table) {
                $table->dropColumn('avg_position');
            });
        }
    }
}
