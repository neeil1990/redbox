<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Services\TelegramBotService;
use Illuminate\Support\Facades\Auth;

class TelegramBot extends Model
{
    protected $guarded = [];
    protected $table = 'telegram_bot';

    public static function sendTestNotify()
    {
        $user = Auth::user();
        if ($user->chat_id) {
            (new TelegramBotService($user->chat_id))->sendMsg(__('Проверка получения уведомлений пройдена!'));
        }
    }

    public static function brokenDomainNotification($project, $chatId)
    {
        $link = TelegramBot::removeProtocol($project);
        $uptimePercent = round($project->uptime_percent, 2);

        $text = __('Project') . " <code>$project->project_name</code>  " . __('broken') . "
        " . __('Check time:') . " <code>$project->last_check</code>
        " . __('http code:') . " <code>$project->code</code>
        " . __('Condition:') . " <code>" . __($project->status) . "</code>
        " . __('Current uptime:') . " <code>$uptimePercent%</code>
        " . __('Go to the website') . "
        <a href='$link' target='_blank'>" . $link . "</a>
        " . __('Go to the service:') . "
        <a href='https://lk.redbox.su/site-monitoring' target='_blank'>https://lk.redbox.su/site-monitoring</a>";

        (new TelegramBotService($chatId))->sendMsg($text);
    }

    public static function repairedDomainNotification($project, $chatId)
    {
        $link = TelegramBot::removeProtocol($project);
        $uptimePercent = round($project->uptime_percent, 2);

        $text = __('Project') . " <code>$project->project_name</code>  " . __('repair') . "
        " . __('Check time:') . " <code>$project->last_check</code>
        " . __('Condition:') . " <code>" . __($project->status) . "</code>
        " . __('Current uptime:') . " <code>$uptimePercent%</code>
        " . __('Total time of the last breakdown:') . " <code>$project->total_time_last_breakdown</code> " . __('min') . "
        " . __('Go to the website') . "
        <a href='$link' target='_blank'>" . $link . "</a>
        " . __('Go to the service:') . "
        <a href='https://lk.redbox.su/site-monitoring' target='_blank'>https://lk.redbox.su/site-monitoring</a>";

        (new TelegramBotService($chatId))->sendMsg($text);
    }

    public static function sendNotificationAboutChangeStateProject($project, $chatId)
    {
        $text =
            __('Domain') .
            ' ' . $project->domain
            . "\n"
            . "\n"
            . $project->domain_information
            . "\n"
            . "\n"
            . __('Go to the service:')
            . " <a href='https://lk.redbox.su/domain-information' target='_blank'>https://lk.redbox.su/domain-information</a>";

        (new TelegramBotService($chatId))->sendMsg($text);
    }

    public static function sendSuccessMessage($chatId)
    {
        $text = __('You have successfully subscribed to the notification newsletter');

        (new TelegramBotService($chatId))->sendMsg($text);
    }

    public static function sendNotificationAboutChangeDNS($project, $chatId, $dns)
    {
        $text = __('Domain') . ' ' . $project->domain
            . "\n"
            . __('DNS CHANGED')
            . "\n"
            . __('old') . " " . $dns
            . "\n"
            . __('new') . " " . $project->dns
            . "\n"
            . "\n"
            . __('Go to the service:')
            . " <a href='https://lk.redbox.su/domain-information' target='_blank'>https://lk.redbox.su/domain-information</a>";

        (new TelegramBotService($chatId))->sendMsg($text);
    }

    public static function sendNotificationAboutExpirationRegistrationPeriod($project, $chatId, $diffInDays)
    {
        $text = __('Domain') . ' ' . $project->domain
            . "\n"
            . __('Notification of the expiration of the registration period')
            . "\n"
            . __('Registration ends after') . " $diffInDays " . __('days')
            . "\n"
            . "\n"
            . __('Go to the service:')
            . " <a href='https://lk.redbox.su/domain-information' target='_blank'>https://lk.redbox.su/domain-information</a>";

        (new TelegramBotService($chatId))->sendMsg($text);
    }

    public static function removeProtocol($project)
    {
        $link = preg_replace('#^https?://#', '', rtrim($project->link, '/'));
        return preg_replace('/^www\./', '', $link);
    }
}
