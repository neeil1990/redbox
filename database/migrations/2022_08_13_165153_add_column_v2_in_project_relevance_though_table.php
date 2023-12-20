<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnV2InProjectRelevanceThoughTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_relevance_though', function (Blueprint $table) {
            $table->longText('cleaning_projects')->nullable();
            $table->boolean('cleaning_state')->default(0);
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
            $table->dropColumn('cleaning_projects');
            $table->dropColumn('cleaning_state');
        });
    }
}
