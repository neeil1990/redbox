<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPositionInMainProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('main_projects', function (Blueprint $table) {
            $table->integer('position')->unique();
        });

        $projects = \App\MainProject::get();
        foreach ($projects as $project) {
            $project->position = $project->id;
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
        Schema::table('main_projects', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
}
