<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCleaningIntervalColumnInClusterConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cluster_configuration', function (Blueprint $table) {
            $table->integer('cleaning_interval')->default(180);
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
            $table->dropColumn('cleaning_interval');
        });
    }
}
