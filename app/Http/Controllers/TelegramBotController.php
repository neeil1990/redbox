<?php

namespace App\Http\Controllers;

use App\TelegramBot;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class TelegramBotController extends Controller
{
    public function verificationToken($token): RedirectResponse
    {
        if (TelegramBot::searchToken($token)) {
            flash()->overlay(__('Now notifications will come to you in a telegram'), ' ')->success();
            return Redirect::back();
        }

        flash()->overlay(__("The project token was not found in the bot's telegram history"), ' ')->error();
        return Redirect::back();
    }

    /**
     * @param $token
     * @return RedirectResponse
     */
    public function resetNotification($token): RedirectResponse
    {
        User::where('telegram_token', '=', $token)->update([
            'telegram_bot_active' => 0,
        ]);
        flash()->overlay(__('You have successfully unsubscribed from the mailing list'), ' ')->success();

        return Redirect::back();
    }
}
