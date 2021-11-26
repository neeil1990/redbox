<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetaTagsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meta_tags_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('meta_tag_id')->unsigned();

            $table->integer('quantity')->unsigned();
            $table->longText('data');
            $table->timestamps();

            $table->foreign('meta_tag_id')
                ->references('id')
                ->on('meta_tags')
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
        Schema::dropIfExists('meta_tags_histories');
    }
}
