<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsSecondInRelevanceHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relevance_history', function (Blueprint $table) {
            $table->longText('html_main_page')->default(null);
            $table->longText('sites')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('relevance_history', function (Blueprint $table) {
            $table->dropColumn('html_main_page');
            $table->dropColumn('sites');
        });
    }
}
