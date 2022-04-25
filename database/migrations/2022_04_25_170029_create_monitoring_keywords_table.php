<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitoringKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_keywords', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('monitoring_progect_id')->unsigned();
            $table->string('query');
            $table->string('page')->nullable();
            $table->timestamps();

            $table->foreign('monitoring_progect_id')
                ->references('id')
                ->on('monitoring_progects')
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
        Schema::dropIfExists('monitoring_keywords');
    }
}
