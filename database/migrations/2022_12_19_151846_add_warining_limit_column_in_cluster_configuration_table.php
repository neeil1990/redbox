<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWariningLimitColumnInClusterConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cluster_configuration', function (Blueprint $table) {
            $table->integer('warning_limit')->default(500);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cluster_configuration', function (Blueprint $table) {
            $table->dropColumn('warning_limit');
        });
    }
}
