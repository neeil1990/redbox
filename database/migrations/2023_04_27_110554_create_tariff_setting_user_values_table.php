<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTariffSettingUserValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariff_setting_user_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tariff_setting_value_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('value');
            $table->timestamps();

            $table->foreign('tariff_setting_value_id')
                ->references('id')
                ->on('tariff_setting_values')
                ->onDelete('cascade');

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
        Schema::dropIfExists('tariff_setting_user_values');
    }
}
