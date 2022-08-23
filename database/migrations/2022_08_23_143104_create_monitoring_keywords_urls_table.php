<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitoringKeywordsUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_keywords_urls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('monitoring_keyword_id')->unsigned();
            $table->string('url')->nullable();
            $table->timestamps();

            $table->foreign('monitoring_keyword_id')
                ->references('id')
                ->on('monitoring_keywords')
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
        Schema::dropIfExists('monitoring_keywords_urls');
    }
}
