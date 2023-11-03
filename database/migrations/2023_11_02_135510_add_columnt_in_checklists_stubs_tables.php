<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumntInChecklistsStubsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checklists_stubs', function (Blueprint $table) {
            $table->dropColumn('classic');
            $table->string('type')->after('tree')->default('personal');
            $table->string('checklist_id')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checklists_stubs', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->string('classic');
            $table->dropColumn('checklist_id');
        });
    }
}
