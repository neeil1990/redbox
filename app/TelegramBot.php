<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\VarDumper\VarDumper;

class TelegramBot extends Model
{
    protected $guarded = [];

    protected $table = 'telegram_bot';

    public static function brokenDomenNotification($project)
    {

        TelegramBot::sendMessage($project, 'недоступен');
    }

    public static function repairedDomenNotification($project)
    {
        TelegramBot::sendMessage($project, 'доступен');
    }

    /**
     * @param false $offset
     * @return array
     */
    public static function getUpdates($offset = null): array
    {
        $data = [];
        if (isset($offset)) {
            $data = ['offset' => $offset];
        }
        $updates = json_decode(file_get_contents('https://api.telegram.org/bot'
            . env('TELEGRAM_BOT_TOKEN', '2073017935:AAHgwY7d0TBAAUzNUyvsmH6QLH14nESQhOc') .
            '/getUpdates?' . http_build_query($data)), true);

        return $updates['result'];
    }

    /**
     * @param $token
     * @return bool
     */
    public static function searchToken($token): bool
    {
        try {
            $find = true;
            $updates = TelegramBot::getUpdates();
            while ($find) {
                foreach ($updates as $key => $element) {
                    if (isset($element['message']) && $element['message']['text'] === $token) {
                        $bot = TelegramBot::where('token', '=', $token)->first();
                        $bot->active = 1;
                        $bot->chat_id = $element['message']['chat']['id'];
                        $bot->save();
                        DomainMonitoring::where('id', '=', $bot->domain_monitoring_id)->update([
                            'send_notification' => 0
                        ]);
                        return true;
                    }
                    if (count($updates) === 1) {
                        return false;
                    }
                    if ($key === array_key_last($updates)) {
                        $updates = TelegramBot::getUpdates($element['update_id']);
                    }
                }
            }
        } catch (\Exception $exception) {
            dd($exception);
        }
    }

    public static function sendMessage($project, $status)
    {
        $uptimePercent = round($project->uptime_percent, 2);

        $data = [
            'text' => "Внимание: проект <code>$project->project_name</code> $status <code>недоступен</code>
<code>$project->last_check</code>
Состояние: <code>$project->status</code>
Текущий uptime: <code>$uptimePercent%</code>
Перейти в сервис:
<a href='https://lk.redbox.su/domain-monitoring'>https://lk.redbox.su/domain-monitoring</a>",
            'chat_id' => $project->telegramBot->chat_id,
            'parse_mode' => 'HTML'
        ];

        file_get_contents('https://api.telegram.org/bot'
            . env('TELEGRAM_BOT_TOKEN', '2073017935:AAHgwY7d0TBAAUzNUyvsmH6QLH14nESQhOc') .
            '/sendMessage?' . http_build_query($data));
    }
}
