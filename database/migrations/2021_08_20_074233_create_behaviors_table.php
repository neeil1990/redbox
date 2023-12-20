<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBehaviorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('behaviors', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->bigInteger('user_id')->unsigned();
            $table->tinyInteger('status')->default(0);
            $table->string('code')->unique();
            $table->string('domain');
            $table->integer('minutes')->unsigned();
            $table->timestamps();

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
        Schema::dropIfExists('behaviors');
    }
}
