<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTelegramBotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('telegram_bot');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('telegram_bot', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('domain_monitoring_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('chat_id')->nullable();
            $table->string('token', 40);
            $table->boolean('active')->default(0);
            $table->timestamps();

            $table->foreign('domain_monitoring_id')
                ->references('id')
                ->on('domain_monitoring')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
}
