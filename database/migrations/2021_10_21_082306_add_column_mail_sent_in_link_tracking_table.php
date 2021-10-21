<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMailSentInLinkTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('link_tracking', function (Blueprint $table) {
            $table->boolean('mail_sent')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('link_tracking', function (Blueprint $table) {
            $table->dropColumn('mail_sent');
        });
    }
}
