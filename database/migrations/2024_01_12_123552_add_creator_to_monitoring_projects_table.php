<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatorToMonitoringProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_projects', function (Blueprint $table) {
            $table->bigInteger('creator')->unsigned()->after('id')->nullable();

            $table->foreign('creator')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitoring_projects', function (Blueprint $table) {
            $table->dropColumn('creator');
        });
    }
}
