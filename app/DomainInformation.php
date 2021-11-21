<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Factory;

class DomainInformation extends Model
{
    protected $table = 'domain_information';

    protected $guarded = [];

    /**
     * @param $project
     * @throws ConnectionException
     * @throws ServerMismatchException
     * @throws WhoisException
     */
    public static function checkDomainSock($project)
    {
        $oldState = $project->broken;
        $oldDNS = $project->dns;
        $whois = Factory::get()->createWhois();
        $project->last_check = Carbon::now();

        $info = $whois->loadDomainInfo($project->domain);
        if (isset($info)) {
            $project->broken = false;
            $project->dns = "DNS:\n" . implode("\n", $info->nameServers);
            $registrationDate = 'Дата регистрации ' . date("Y-m-d", $info->creationDate);
            $freeDate = date("Y-m-d", $info->expirationDate);
            $project->domain_information = DomainInformation::prepareStatus($project->dns, $registrationDate, $freeDate);
            DomainInformation::sendNotifications($project, $oldState, $oldDNS, $freeDate);
        } else {
            $project->broken = true;
            $project->domain_information = __("No records were found for the selected source");
            DomainInformation::sendNotifications($project, $oldState);
        }
        $project->save();
    }

    /**
     * @param $text
     * @return bool
     */
    public static function isNoEntries($text): bool
    {
        return preg_match('/(No entries found for the selected source\(s\).)/',
            $text, $matches, PREG_OFFSET_CAPTURE);
    }

    /**
     * @param $text
     * @return string
     */
    public static function getCreationDate($text): string
    {
        preg_match('/(created:)(\s\s\s\s\s\s\s)(.*)(\n)/', $text, $matches, PREG_OFFSET_CAPTURE);
        return __('Domain created') . ' ' . substr($matches[3][0], 0, 10);
    }

    /**
     * @param $text
     * @return string
     */
    public static function getDNS($text): string
    {
        $dns = '';
        preg_match_all('/(nserver:)(\s\s\s\s\s\s\s)(.*)(\n)/', $text, $matches, PREG_OFFSET_CAPTURE);
        if (empty($matches[0])) {
            $dns = __('not found');
        } else {
            foreach ($matches[0] as $item) {
                $dns .= $item[0];
            }
        }
        $dns = str_replace('nserver:       ', '', $dns);
        return "DNS: \n" . $dns;
    }

    public static function prepareStatus($dns, $registrationDate, $freeDate): string
    {
        $date = new Carbon($freeDate);
        return $dns . "\n\n"
            . $registrationDate . "\n"
            . __('Registration expires')
            . $freeDate
            . ' '
            . __('through')
            . ' '
            . $date->diffInDays(Carbon::now())
            . ' '
            . __('days');
    }

    /**
     * @param $text
     * @param $project
     * @return string
     */
    public static function checkDate($text, $project): string
    {
        preg_match('/(free-date:)(\s\s\s\s\s)(.*)(\n)/', $text, $matches, PREG_OFFSET_CAPTURE);
        $freeDate = new Carbon($matches[3][0]);
        $countDays = $freeDate->diffInDays(Carbon::now());
        if ($countDays <= 10) {
            $project->broken = true;
        } else {
            $project->broken = false;
        }
        return substr($matches[3][0], 0, 10);
    }

    /**
     * @param $project
     * @param $oldState
     * @param $oldDNS
     * @param $freeDate
     */
    public static function sendNotifications($project, $oldState, $oldDNS = null, $freeDate = null)
    {
        $user = User::find($project->user_id);
        if ($project->broken != $oldState) {
            if ($user->telegram_bot_active) {
                TelegramBot::sendNotificationAboutChangeStateProject($project, $user->chat_id);
            }
            $user->DomainInformationNotification($project);
        }
        if ($project->check_dns && $project->dns !== $oldDNS && isset($oldDNS)) {
            if ($user->telegram_bot_active) {
                TelegramBot::sendNotificationAboutChangeDNS($project, $user->chat_id, $oldDNS);
            }
            $user->sendNotificationAboutChangeDNS($project);
        }
        if ($project->check_registration_date && isset($freeDate)) {
            $freeDate = new Carbon($freeDate);
            $diffInDays = $freeDate->diffInDays(Carbon::now());
            if ($diffInDays < 20) {
                if ($user->telegram_bot_active) {
                    TelegramBot::sendNotificationAboutExpirationRegistrationPeriod($project, $user->chat_id, $diffInDays);
                }
                $user->sendNotificationAboutExpirationRegistrationPeriod($project, $diffInDays);
            }
        }
    }

    /**
     * @param $domain
     * @return bool
     */
    public
    static function isValidDomain($domain): bool
    {
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain) //valid chars check
            && preg_match("/^.{1,253}$/", $domain) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain)); //length of each label
    }


    /**
     * @param $link
     * @return string
     */
    public
    static function getDomain($link): string
    {
        $domain = preg_replace("#^[^:/.]*[:/]+#i", '', $link);
        $domain = preg_replace('/www./', '', $domain);
        $domain = explode(':', $domain);
        $domain = explode('/', $domain[0]);
        return $domain[0];
    }
}
