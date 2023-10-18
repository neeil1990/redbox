<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMasteredAndMasteredPercentToMonitoringDataTableColumnsProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_data_table_columns_projects', function (Blueprint $table) {
            $table->decimal('mastered', 8, 2)->nullable()->after('middle');
            $table->decimal('mastered_percent', 8, 2)->nullable()->after('middle');
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
            $table->dropColumn('mastered');
            $table->dropColumn('mastered_percent');
        });
    }
}
