<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class AddTelegramTokenToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_token', 100)->nullable();
            $table->boolean('telegram_bot_active')->default(false);
            $table->string('chat_id')->nullable();
        });

        $users = \App\User::all();
        foreach ($users as $user) {
            $user->telegram_token = str_shuffle(Str::random(50) . Carbon::now());
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('telegram_token');
            $table->dropColumn('telegram_bot_active');
            $table->dropColumn('chat_id');
        });
    }
}
