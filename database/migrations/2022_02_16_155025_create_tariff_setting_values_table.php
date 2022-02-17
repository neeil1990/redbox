<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTariffSettingValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariff_setting_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tariff_setting_id')->unsigned();
            $table->string('tariff');
            $table->integer('value');
            $table->timestamps();

            $table->foreign('tariff_setting_id')
                ->references('id')
                ->on('tariff_settings')
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
        Schema::dropIfExists('tariff_setting_values');
    }
}
