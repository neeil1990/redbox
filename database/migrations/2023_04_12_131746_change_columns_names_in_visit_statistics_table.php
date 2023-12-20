<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsNamesInVisitStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visit_statistics', function (Blueprint $table) {
            $table->integer('counter')->default(0)->change();
            $table->renameColumn('counter', 'actions_counter');
            $table->integer('refresh_page_counter')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visit_statistics', function (Blueprint $table) {
            $table->renameColumn('actions_counter', 'counter');
            $table->dropColumn('refresh_page_counter');
        });
    }
}
