<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\VarDumper\VarDumper;

class TelegramBot extends Model
{
    protected $guarded = [];

    protected $table = 'telegram_bot';

    public static function brokenDomenNotification($project, $chatId)
    {
        TelegramBot::sendMessage($project, 'недоступен', $chatId);
    }

    public static function repairedDomenNotification($project, $chatId)
    {
        TelegramBot::sendMessage($project, 'доступен', $chatId);
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
        $find = true;
        $updates = TelegramBot::getUpdates();
        while ($find) {
            foreach ($updates as $key => $element) {
                if (isset($element['message']) &&
                    isset($element['message']['text']) &&
                    $element['message']['text'] === $token
                ) {
                    User::where('telegram_token', '=', $token)->update([
                        'telegram_bot_active' => 1,
                        'chat_id' => $element['message']['chat']['id'],
                    ]);
                    TelegramBot::sendSuccessMessage($element['message']['chat']['id']);
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
    }

    public static function sendSuccessMessage($chatId)
    {
        $data = [
            'text' => 'Вы успешно подписались на рассылку уведомлений',
            'chat_id' => $chatId,
            'parse_mode' => 'HTML'
        ];

        file_get_contents('https://api.telegram.org/bot'
            . env('TELEGRAM_BOT_TOKEN', '2073017935:AAHgwY7d0TBAAUzNUyvsmH6QLH14nESQhOc') .
            '/sendMessage?' . http_build_query($data));
    }

    /**
     * @param $project
     * @param $status
     * @param $chatId
     */
    public static function sendMessage($project, $status, $chatId)
    {
        $uptimePercent = round($project->uptime_percent, 2);
        if ($status === 'доступен') {
            $text = "Внимание: проект <code>$project->project_name</code> $status
Время проверки: <code>$project->last_check</code>
Состояние: <code>$project->status</code>
Текущий uptime: <code>$uptimePercent%</code>
Общее время последней поломки: <code>$project->total_time_last_breakdown</code> минут
Перейти в сервис:
<a href='https://lk.redbox.su/domain-monitoring'>https://lk.redbox.su/domain-monitoring</a>";
        } else {
            $text = "Внимание: проект <code>$project->project_name $status</code>
Время проверки: <code>$project->last_check</code>
Состояние: <code>$project->status</code>
Текущий uptime: <code>$uptimePercent%</code>
Перейти в сервис:
<a href='https://lk.redbox.su/domain-monitoring'>https://lk.redbox.su/domain-monitoring</a>";
        }

        $data = [
            'text' => $text,
            'chat_id' => $chatId,
            'parse_mode' => 'HTML'
        ];

        file_get_contents('https://api.telegram.org/bot'
            . env('TELEGRAM_BOT_TOKEN', '2073017935:AAHgwY7d0TBAAUzNUyvsmH6QLH14nESQhOc') .
            '/sendMessage?' . http_build_query($data));
    }
}
