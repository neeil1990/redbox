<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalyzeRelevanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analyze_relevance', function (Blueprint $table) {
            $table->bigIncrements('id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('phrase');
            $table->string('main_page_link');
            $table->integer('count');
            $table->integer('region');
            $table->text('ignored_domains')->nullable();
            $table->string('check_no_index')->nullable();
            $table->string('check_hidden_text')->nullable();
            $table->string('check_parts_of_speech')->nullable();
            $table->string('remove_list_words')->nullable();
            $table->text('list_words')->nullable();
            $table->text('sites')->nullable();
            $table->longText('html_relevance')->nullable();
            $table->longText('html_main_page')->nullable();
            $table->string('xml_hash');
            $table->string('config_hash');

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
        Schema::dropIfExists('analyze_relevance');
    }
}
