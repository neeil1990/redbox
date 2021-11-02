<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainMonitoringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_monitoring', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('project_name', 100);
            $table->text('link');
            $table->integer('timing');
            $table->string('phrase', 100)->nullable();
            $table->string('status')->nullable();
            $table->integer('code')->nullable();
            $table->boolean('broken')->default(false);
            $table->float('uptime_percent')->nullable();
            $table->integer('up_time')->nullable();
            $table->timestamp('last_check')->nullable();
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
        Schema::dropIfExists('domain_monitoring');
    }
}
