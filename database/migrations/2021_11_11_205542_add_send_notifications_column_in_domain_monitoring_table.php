<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSendNotificationsColumnInDomainMonitoringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domain_monitoring', function (Blueprint $table) {
            $table->boolean('send_notification')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domain_monitoring', function (Blueprint $table) {
            $table->dropColumn('send_notification');
        });
    }
}
