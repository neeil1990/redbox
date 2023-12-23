<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexInSearchIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_indices', function (Blueprint $table) {
            $table->index('position');
            $table->index('lr');
            $table->index('query');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_indices', function (Blueprint $table) {
            $table->dropIndex('position');
            $table->dropIndex('lr');
            $table->dropIndex('query');
        });
    }
}
