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
            flash()->overlay(__('Теперь уведомления будут приходить к вам в телеграм'), ' ')->success();
            return Redirect::back();
        }

        flash()->overlay(__('Токен проекта не найден в истории телеграм бота'), ' ')->error();
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
        flash()->overlay(__('Вы успешно отписались от рассылки'), ' ')->success();

        return Redirect::back();
    }
}
