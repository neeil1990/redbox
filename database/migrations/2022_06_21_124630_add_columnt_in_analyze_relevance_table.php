<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumntInAnalyzeRelevanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analyze_relevance', function (Blueprint $table) {
            $table->text('page_hash');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('analyze_relevance', function (Blueprint $table) {
            $table->dropColumn('page_hash');
        });
    }
}
