<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleDescriptionKeywordsToMetaTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meta_tags', function (Blueprint $table) {

            $table->integer('length_title_min')->unsigned()->nullable();
            $table->integer('length_title_max')->unsigned()->nullable();

            $table->integer('length_description_min')->unsigned()->nullable();
            $table->integer('length_description_max')->unsigned()->nullable();

            $table->integer('length_keywords_min')->unsigned()->nullable();
            $table->integer('length_keywords_max')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meta_tags', function (Blueprint $table) {
            $table->dropColumn('length_title_min');
            $table->dropColumn('length_title_max');

            $table->dropColumn('length_description_min');
            $table->dropColumn('length_description_max');

            $table->dropColumn('length_keywords_min');
            $table->dropColumn('length_keywords_max');
        });
    }
}
