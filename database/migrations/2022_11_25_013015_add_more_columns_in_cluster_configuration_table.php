<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreColumnsInClusterConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cluster_configuration', function (Blueprint $table) {
            $table->boolean('brut_force')->default(0);
            $table->boolean('search_relevance')->default(0);
            $table->boolean('search_base')->default(1);
            $table->string('search_engine')->default('yandex');
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
            $table->boolean('brut_force');
            $table->boolean('search_relevance');
            $table->boolean('search_base');
        });
    }
}
