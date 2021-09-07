<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClicksAndPagesToBehaviorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('behaviors', function (Blueprint $table) {
            $table->integer('clicks')->unsigned()->after('minutes');
            $table->integer('pages')->unsigned()->after('minutes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('behaviors', function (Blueprint $table) {
            $table->dropColumn('clicks');
            $table->dropColumn('pages');
        });
    }
}
