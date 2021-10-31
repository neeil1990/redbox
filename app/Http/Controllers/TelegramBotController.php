<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TelegramBotController extends Controller
{
    public function setWebhook()
    {
        $url = 'https://api.telegram.org/bot2021809943:AAEYwR44bYSl00FzSdpGjIPykZswS1IN1ko/setWebhook';
        $options = [
            'url' => 'https://lk.redbox.su/telegrambot.php'
        ];

        $response = file_get_contents($url . '?' . http_build_query($options));
        dd($response);
    }
}
