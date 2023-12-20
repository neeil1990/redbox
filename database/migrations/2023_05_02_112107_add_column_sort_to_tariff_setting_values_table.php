<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSortToTariffSettingValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tariff_setting_values', function (Blueprint $table) {
            $table->integer('sort')->default(500)->unsigned()->after('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tariff_setting_values', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
}
