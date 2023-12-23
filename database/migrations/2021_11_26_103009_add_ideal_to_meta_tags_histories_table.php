<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdealToMetaTagsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meta_tags_histories', function (Blueprint $table) {
            $table->boolean('ideal')->default(false)->after('meta_tag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meta_tags_histories', function (Blueprint $table) {
            $table->dropColumn('ideal');
        });
    }
}
