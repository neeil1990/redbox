<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotifyCheckboxToDomainInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domain_information', function (Blueprint $table) {
            $table->boolean('check_dns_email')->default(0)->after('check_dns');
            $table->boolean('check_registration_date_email')->default(0)->after('check_registration_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domain_information', function (Blueprint $table) {
            $table->dropColumn('check_dns_email');
            $table->dropColumn('check_registration_date_email');
        });
    }
}
