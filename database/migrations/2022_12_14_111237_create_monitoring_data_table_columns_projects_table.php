<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitoringDataTableColumnsProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_data_table_columns_projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('monitoring_project_id');
            $table->integer('words')->nullable();
            $table->integer('middle')->nullable();
            $table->decimal('top3', 8, 2)->nullable();
            $table->string('diff_top3')->nullable();
            $table->decimal('top5', 8, 2)->nullable();
            $table->string('diff_top5')->nullable();
            $table->decimal('top10', 8, 2)->nullable();
            $table->string('diff_top10')->nullable();
            $table->decimal('top30', 8, 2)->nullable();
            $table->string('diff_top30')->nullable();
            $table->decimal('top100', 8, 2)->nullable();
            $table->string('diff_top100')->nullable();
            $table->timestamps();

            $table->foreign('monitoring_project_id', 'mp_id_foreign')
                ->references('id')->on('monitoring_projects')
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
        Schema::dropIfExists('monitoring_data_table_columns_projects');
    }
}
