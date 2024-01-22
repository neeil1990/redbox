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
            $table->boolean('weekends')->default(0)->after('active_after');
            $table->dateTime('next_activate')->after('weekends')->nullable();
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
            $table->dropColumn('next_activate');
        });
    }
}
