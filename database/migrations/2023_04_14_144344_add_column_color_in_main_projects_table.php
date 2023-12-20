<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnColorInMainProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('main_projects', function (Blueprint $table) {
            $table->string('color')->after('controller')->unique()->nullable();
        });

        $projects = \App\MainProject::get();
        foreach ($projects as $project) {
            $project->color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
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
            $table->dropColumn('color');
        });
    }
}
