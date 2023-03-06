<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->integer('position');
            $table->timestamps();
        });

        Schema::create('partners_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name_ru')->nullable();
            $table->string('name_en')->nullable();
            $table->string('link_ru')->nullable();
            $table->string('link_en')->nullable();
            $table->string('short_link_ru')->nullable();
            $table->string('short_link_en')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_en')->nullable();
            $table->boolean('auditorium_ru')->default(false);
            $table->boolean('auditorium_en')->default(false);
            $table->integer('position')->unique();

            $table->text('image');

            $table->unsignedBigInteger('partners_groups_id');
            $table->foreign('partners_groups_id')
                ->references('id')->on('partners_groups')
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
        Schema::table('partners_items', function (Blueprint $table) {
            $table->dropForeign(['partners_groups_id']);
        });

        Schema::dropIfExists('partners_groups');
        Schema::dropIfExists('partners_items');
    }
}
