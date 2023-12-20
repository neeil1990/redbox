<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMenuPositionsInProjectPositionAtTheUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_position_at_the_user', function (Blueprint $table) {
            $table->string('menu_positions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_position_at_the_user', function (Blueprint $table) {
            $table->dropColumn('menu_positions');
        });
    }
}
