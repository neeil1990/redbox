<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePolicyAndTermsDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policy_and_terms_docs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('policy_ru');
            $table->longText('policy_en');
            $table->longText('terms_ru');
            $table->longText('terms_en');

            $table->unsignedBigInteger('last_editor');

            $table->foreign('last_editor')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

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
        Schema::dropIfExists('policy_and_terms_docs');
    }
}
