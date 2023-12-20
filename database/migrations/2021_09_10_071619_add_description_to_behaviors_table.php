<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionToBehaviorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('behaviors', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('clicks');
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
            $table->dropColumn('description');
        });
    }
}
