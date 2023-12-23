<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_access', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('access_id');

            $table->foreign('project_id')
                ->references('id')->on('project_relevance_history')
                ->onDelete('cascade');

            $table->foreign('author_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('access_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_access');
    }
}
