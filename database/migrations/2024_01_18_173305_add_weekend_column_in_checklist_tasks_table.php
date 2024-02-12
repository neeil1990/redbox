<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWeekendColumnInChecklistTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checklist_tasks', function (Blueprint $table) {
            $table->boolean('weekends')->default(null)->after('active_after');
            $table->integer('repeat_every')->after('weekends')->nullable();
            $table->integer('deadline_every')->after('repeat_every')->nullable();
            $table->dateTime('end_date')->after('deadline')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checklist_tasks', function (Blueprint $table) {
            $table->dropColumn('weekends');
            $table->dropColumn('repeat_every');
            $table->dropColumn('deadline_every');
            $table->dropColumn('end_date');
        });
    }
}
