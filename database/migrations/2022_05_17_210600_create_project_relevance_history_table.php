<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectRelevanceHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_relevance_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('last_check');
            $table->integer('total_points')->nullable();
            $table->integer('count_sites')->nullable();
            $table->string('group_name')->default('no name');
            $table->timestamps();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('project_relevance_history');
    }
}
