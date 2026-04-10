<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInAiGenerationHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ai_generation_histories', function (Blueprint $table) {
            $table->integer('used_tokens')->default(0)->after('result');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ai_generation_histories', function (Blueprint $table) {
            $table->dropColumns(['used_tokens']);
        });
    }
}
