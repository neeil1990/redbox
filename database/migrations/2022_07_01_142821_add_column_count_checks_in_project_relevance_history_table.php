<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCountChecksInProjectRelevanceHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_relevance_history', function (Blueprint $table) {
            $table->integer('count_checks')->default(0);
        });

        $projects = App\ProjectRelevanceHistory::all();

        foreach ($projects as $project) {
            $project->count_checks = App\RelevanceHistory::where('project_relevance_history_id', '=', $project->id)
                ->count();

            $project->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_relevance_history', function (Blueprint $table) {
            $table->dropColumn('count_checks');
        });
    }
}
