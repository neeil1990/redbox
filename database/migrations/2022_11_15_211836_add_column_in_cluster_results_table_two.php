<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInClusterResultsTableTwo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cluster_results', function (Blueprint $table) {
            $table->string('progress_id', 255);
            $table->boolean('show')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cluster_results', function (Blueprint $table) {
            $table->dropColumn('progress_id');
            $table->dropColumn('show');
        });
    }
}
