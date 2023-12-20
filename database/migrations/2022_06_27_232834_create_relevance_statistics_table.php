<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelevanceStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relevance_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('date');
            $table->integer('count_checks')->default(0);
            $table->integer('count_fails')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relevance_statistics');
    }
}
