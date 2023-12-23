<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchIndicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_indices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('source')->nullable();
            $table->string('lr')->nullable();
            $table->text('url');
            $table->integer('position')->unsigned();
            $table->string('title')->nullable();
            $table->text('snippet')->nullable();
            $table->string('query')->nullable();
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
        Schema::dropIfExists('search_indices');
    }
}
