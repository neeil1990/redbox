<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrokenLinkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('broken_link', function (Blueprint $table) {
            $table->bigIncrements('link_tracking_id');
            $table->string('status');
            $table->timestamps();

            $table->foreign('link_tracking_id')
                ->references('id')
                ->on('link_tracking')
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
        Schema::dropIfExists('broken_link');
    }
}
