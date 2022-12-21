<?php

use App\Models\Cluster\ClusterConfigurationClassic;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClusterConfigurationClassicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cluster_configuration_classic', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('region')->default(213);
            $table->integer('count')->default(40);
            $table->integer('warning_limit')->default(500);

            $table->string('clustering_level')->default('hard');
            $table->string('engine_version')->default('latest');
            $table->string('search_engine')->default('yandex');

            $table->boolean('save_results')->default(1);
            $table->boolean('search_base')->default(0);
            $table->boolean('search_phrased')->default(0);
            $table->boolean('search_target')->default(0);
            $table->boolean('send_message')->default(0);
            $table->boolean('brut_force')->default(0);
            $table->boolean('search_relevance')->default(0);

            $table->timestamps();
        });

        $conf = new ClusterConfigurationClassic();
        $conf->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cluster_configuration_classic');
    }
}
