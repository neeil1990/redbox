<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DomainInformation extends Model
{
    protected $table = 'domain_information';

    protected $guarded = [];

    /**
     * @param $project
     */
    public static function checkDomainSock($project)
    {
        $oldState = $project->broken;
        $socket = fsockopen('whois.tcinet.ru', 43);
        $project->last_check = Carbon::now();
        if ($socket) {
            fputs($socket, $project->domain . PHP_EOL);
            $text = '';
            while (!feof($socket)) {
                $text .= fgets($socket, 128);
            }
            fclose($socket);
            if (preg_match('/(No entries found for the selected source\(s\).)/', $text, $matches, PREG_OFFSET_CAPTURE)) {
                $project->domain_information = __("No records were found for the selected source");
                $project->broken = true;
            } else {
                $dns = DomainInformation::getDNS($text);
                $registrationDate = DomainInformation::getCreationDate($text);
                $freeDate = DomainInformation::checkDate($text, $project);
                $project->domain_information = DomainInformation::prepareStatus($dns, $registrationDate, $freeDate);
            }
        } else {
            $project->domain_information = __("No records were found for the selected source");
            $project->broken = true;
        }
        $project->save();
        DomainInformation::sendNotification($project, $oldState);
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
    public static function getDNS($text)
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
        return $dns . "\n"
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
     */
    public static function sendNotification($project, $oldState)
    {
        $user = User::find($project->user_id);
        if ($project->broken != $oldState) {
            if ($user->telegram_bot_active) {
                TelegramBot::prepareDomainInformationMessage($project, $user->chat_id);
            }
            $user->DomainInformationNotification($project);
        }
    }

    /**
     * @param $domain
     * @return bool
     */
    public static function isValidDomain($domain): bool
    {
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain) //valid chars check
            && preg_match("/^.{1,253}$/", $domain) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain)); //length of each label
    }


    /**
     * @param $link
     * @return string
     */
    public static function getDomain($link): string
    {
        $domain = preg_replace("#^[^:/.]*[:/]+#i", '', $link);
        $domain = preg_replace('/www./', '', $domain);
        $domain = explode(':', $domain);
        $domain = explode('/', $domain[0]);
        return $domain[0];
    }
}
