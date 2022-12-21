<?php

use App\Models\Cluster\ClusterConfiguration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClusterConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cluster_configuration', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('region')->default(213);
            $table->integer('count')->default(20);
            $table->string('clustering_level')->default('soft');
            $table->string('engine_version')->default('new');
            $table->boolean('save_results')->default(1);
            $table->boolean('search_phrased')->default(0);
            $table->boolean('search_target')->default(0);

            $table->timestamps();
        });

        $config = new ClusterConfiguration();

        $config->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cluster_configuration');
    }
}
