<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiGenerationStopWordCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('ai_generation_stop_word_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('name', 255);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('ai_generation_stop_words', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('user_id');

            $table->foreign('category_id')
                ->references('id')
                ->on('ai_generation_stop_word_categories')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_generation_stop_word_categories');

        Schema::table('ai_generation_stop_words', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
}