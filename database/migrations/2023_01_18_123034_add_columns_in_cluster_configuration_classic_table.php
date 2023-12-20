<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInClusterConfigurationClassicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cluster_configuration_classic', function (Blueprint $table) {
            $table->integer('gain_factor')->default(15);
            $table->string('reduction_ratio')->default('pre-hard');
            $table->integer('brut_force_count')->default(3);
            $table->text('ignored_domains')->default("");
            $table->text('ignored_words')->default("");
        });

        Schema::table('cluster_configuration', function (Blueprint $table) {
            $table->integer('gain_factor')->default(15);
            $table->string('reduction_ratio')->default('pre-hard');
            $table->integer('brut_force_count')->default(3);
            $table->text('ignored_domains')->default("");
            $table->text('ignored_words')->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cluster_configuration_classic', function (Blueprint $table) {
            $table->dropColumn('gain_factor');
            $table->dropColumn('reduction_ratio');
            $table->dropColumn('brut_force_count');
            $table->dropColumn('ignored_domains');
            $table->dropColumn('ignored_words');
        });

        Schema::table('cluster_configuration', function (Blueprint $table) {
            $table->dropColumn('gain_factor');
            $table->dropColumn('reduction_ratio');
            $table->dropColumn('brut_force_count');
            $table->dropColumn('ignored_domains');
            $table->dropColumn('ignored_words');
        });
    }
}
