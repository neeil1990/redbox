<?php

namespace App\Http\Controllers;

use App\TelegramBot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    public function verificationToken($token): RedirectResponse
    {
        $updates = Telegram::getUpdates();
        dd($updates);
        foreach ($updates as $update) {
            if ($update['message']['text'] === $token) {
                TelegramBot::where('token', '=', $token)->update([
                    'active' => 1,
                    'chat_id' => $update['message']['chat']['id']
                ]);
                flash()->overlay(__('Теперь уведомления будут приходить к вам в телеграм'), ' ')->success();
                return Redirect::back();
            }
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
        TelegramBot::where('token', '=', $token)->update([
            'active' => 0,
        ]);
        flash()->overlay(__('Вы успешно отписались от рассылки'), ' ')->success();

        return Redirect::back();
    }
}
