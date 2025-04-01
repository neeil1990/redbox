<?php


namespace App\Services;

use App\User;

class TelegramBotService
{
    protected $url = 'https://api.telegram.org/bot';
    protected $token;
    protected $chat_id;

    public function __construct(int $chat_id)
    {
        $this->token = config('app.telegram_bot_token');
        $this->setChatId($chat_id);
    }

    public function updateUserChatID(string $email)
    {
        return User::where('email', $email)->update(['chat_id' => $this->getChatId(), 'telegram_bot_active' => 1]);
    }

    public function sendMsg(string $text)
    {
        $url = $this->api();

        file_get_contents("$url/sendMessage?" . http_build_query([
            'text' => $text,
            'chat_id' => $this->getChatId(),
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ]));
    }

    public function getChatId(): int
    {
        return $this->chat_id;
    }

    public function setChatId($chat_id): void
    {
        $this->chat_id = $chat_id;
    }

    private function api()
    {
        return $this->url . $this->token;
    }
}
