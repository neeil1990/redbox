<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelevanceSharingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relevance_sharing', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('owner_id');
            $table->integer('access')->default(0);

            $table->foreign('user_id')
                ->on('users')
                ->references('id')
                ->onDelete('cascade');

            $table->foreign('owner_id')
                ->on('users')
                ->references('id')
                ->onDelete('cascade');

            $table->foreign('project_id')
                ->on('project_relevance_history')
                ->references('id')
                ->onDelete('cascade');

            $table->index('user_id');
            $table->index('project_id');
            $table->index('owner_id');

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
        Schema::dropIfExists('relevance_sharing');
    }
}
