<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInRelevanceHistoryTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relevance_history', function (Blueprint $table) {
            $table->boolean('state')->default(0);
        });

        $stories = App\RelevanceHistory::all();
        foreach ($stories as $story) {
            $story->state = 1;
            $story->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('relevance_history', function (Blueprint $table) {
            $table->dropColumn('state');
        });
    }
}
