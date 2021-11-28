<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameLangthColumnToMetaTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meta_tags', function (Blueprint $table) {

            $table->renameColumn('length_title_min', 'title_min');
            $table->renameColumn('length_title_max', 'title_max');
            $table->renameColumn('length_description_min', 'description_min');
            $table->renameColumn('length_description_max', 'description_max');
            $table->renameColumn('length_keywords_min', 'keywords_min');
            $table->renameColumn('length_keywords_max', 'keywords_max');
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

            $table->renameColumn('title_min', 'length_title_min');
            $table->renameColumn('title_max', 'length_title_max');
            $table->renameColumn('description_min', 'length_description_min');
            $table->renameColumn('description_max', 'length_description_max');
            $table->renameColumn('keywords_min', 'length_keywords_min');
            $table->renameColumn('keywords_max', 'length_keywords_max');
        });
    }
}
