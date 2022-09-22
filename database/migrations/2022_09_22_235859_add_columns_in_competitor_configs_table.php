<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInCompetitorConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competitor_configs', function (Blueprint $table) {
            $table->integer('count_repeat_top_10')->default(5);
            $table->integer('count_repeat_top_20')->default(10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competitor_configs', function (Blueprint $table) {
            $table->dropColumn('count_repeat_top_10');
            $table->dropColumn('count_repeat_top_20');
        });
    }
}
