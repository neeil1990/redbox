<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequestColumnInRelevanceHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relevance_history', function (Blueprint $table) {
            $table->longText('request')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('relevance_history', 'request')) {
            Schema::table('relevance_history', function (Blueprint $table) {
                $table->dropColumn('request');
            });
        }
    }
}
