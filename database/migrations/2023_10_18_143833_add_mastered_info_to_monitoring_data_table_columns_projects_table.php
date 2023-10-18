<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMasteredInfoToMonitoringDataTableColumnsProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_data_table_columns_projects', function (Blueprint $table) {
            $table->text('mastered_info')->nullable()->after('mastered');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitoring_data_table_columns_projects', function (Blueprint $table) {
            $table->dropColumn('mastered_info');
        });
    }
}
