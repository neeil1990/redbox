<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBehaviorsPhrasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('behaviors_phrases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('behavior_id');
            $table->tinyInteger('status')->default(0);
            $table->string('code')->unique();
            $table->string('phrase');
            $table->timestamps();

            $table->foreign('behavior_id')
                ->references('id')
                ->on('behaviors')
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
        Schema::dropIfExists('behaviors_phrases');
    }
}
