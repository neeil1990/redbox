<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTotalTimeLastBreakdownToDomainMonitoringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domain_monitoring', function (Blueprint $table) {
            $table->string('total_time_last_breakdown')->nullable();
            $table->string('time_last_breakdown')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domain_monitoring', function (Blueprint $table) {
            $table->dropColumn('total_time_last_breakdown');
            $table->dropColumn('time_last_breakdown');
        });
    }
}
