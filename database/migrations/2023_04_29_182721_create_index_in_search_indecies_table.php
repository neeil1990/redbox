<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexInSearchIndeciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_indices', function (Blueprint $table) {
            $table->index(['position', 'query', 'lr']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('search_indices', function (Blueprint $table) {
            $table->dropIndex(['position', 'query', 'lr']);
        });
    }
}
