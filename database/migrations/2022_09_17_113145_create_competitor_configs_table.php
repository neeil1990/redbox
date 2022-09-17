<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompetitorConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitor_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('agrigators')->nullable();
            $table->timestamps();
        });

        $config = new \App\CompetitorConfig();
        $config->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competitor_configs');
    }
}
