<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateChecklistTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checklist_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('checklist_tasks', 'next_activate')) {
                $table->dropColumn('next_activate');
            }

            if (!Schema::hasColumn('checklist_tasks', 'end_date')) {
                $table->dateTime('end_date')->after('weekends')->nullable();
            }

            if (!Schema::hasColumn('checklist_tasks', 'deadline_every')) {
                $table->integer('deadline_every')->after('weekends')->nullable();
            }

            if (!Schema::hasColumn('checklist_tasks', 'repeat_every')) {
                $table->integer('repeat_every')->after('weekends')->nullable();
            }
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
            if (Schema::hasColumn('checklist_tasks', 'end_date')) {
                $table->dropColumn('end_date');
            }

            if (Schema::hasColumn('checklist_tasks', 'deadline_every')) {
                $table->dropColumn('deadline_every');
            }

            if (Schema::hasColumn('checklist_tasks', 'repeat_every')) {
                $table->dropColumn('repeat_every');
            }
        });
    }
}
