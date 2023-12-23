<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChecklistProjectChecklistLabelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_project_checklist_label', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('checklist_project_id');
            $table->unsignedBigInteger('checklist_label_id');
            $table->timestamps();

            $table->foreign('checklist_project_id')
                ->references('id')
                ->on('checklist_projects')
                ->onDelete('cascade');

            $table->foreign('checklist_label_id')
                ->references('id')
                ->on('check_lists_labels')
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
        Schema::dropIfExists('checklist_project_checklist_label');
    }
}
