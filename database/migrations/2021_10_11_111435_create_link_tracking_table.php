<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_tracking', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_tracking_id');
            $table->text('link');
            $table->text('site_donor');
            $table->text('anchor');
            $table->boolean('noindex');
            $table->boolean('nofollow');
            $table->boolean('yandex');
            $table->boolean('google');
            $table->string('last_check')->nullable();
            $table->string('status')->default('not checked');
            $table->boolean('broken')->nullable();
            $table->timestamps();

            $table->foreign('project_tracking_id')
                ->references('id')
                ->on('project_tracking')
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
        Schema::dropIfExists('link_tracking');
    }
}
