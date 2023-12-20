<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBudgetToMonitoringProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_projects', function (Blueprint $table) {
            $table->decimal('budget', 8, 2)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitoring_projects', function (Blueprint $table) {
            $table->dropColumn('budget');
        });
    }
}
