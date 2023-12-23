<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChecklistTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id');
            $table->string('name');
            $table->string('status')->index();
            $table->longText('description')->nullable();
            $table->boolean('subtask')->default(0);
            $table->integer('task_id')->nullable();
            $table->dateTime('deadline');
            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')
                ->on('checklist_projects')
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
        Schema::dropIfExists('checklist_tasks');
    }
}
