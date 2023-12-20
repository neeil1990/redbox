<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveRowsInAnalyzeRelevanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analyze_relevance', function (Blueprint $table) {
            $table->dropColumn('xml_hash');
            $table->dropColumn('count');
            $table->dropColumn('region');
            $table->dropColumn('ignored_domains');
            $table->dropColumn('config_hash');
            $table->dropColumn('list_words');
            $table->dropColumn('remove_list_words');
            $table->dropColumn('check_parts_of_speech');
            $table->dropColumn('check_hidden_text');
            $table->dropColumn('check_no_index');
            $table->dropColumn('phrase');
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
            //
        });
    }
}
