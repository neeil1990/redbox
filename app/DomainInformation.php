<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\VarDumper\VarDumper;

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
        $dns = '';
        $registrationDate = '';
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
                $project->domain_information = "No records were found for the selected source or \nthe source is hidden from the database";
                $project->broken = true;
                DomainInformation::sendNotification($project, $oldState);
                return;
            }
            if ($project->check_dns) {
                preg_match_all('/(nserver:)(\s\s\s\s\s\s\s)(ns.*)(\n)/', $text, $matches, PREG_OFFSET_CAPTURE);
                $dns = '';
                foreach ($matches[0] as $item) {
                    $dns .= $item[0];
                }
            }
            if ($project->check_registration_date) {
                preg_match('/(created:)(\s\s\s\s\s\s\s)(.*)(\n)/', $text, $matches, PREG_OFFSET_CAPTURE);
                $registrationDate = $matches[0][0];
            }
            $freeDate = DomainInformation::checkDate($text, $project);
            $project->domain_information = DomainInformation::prepareStatus($dns, $registrationDate, $freeDate);
            DomainInformation::sendNotification($project, $oldState);
        }
    }

    public static function prepareStatus($dns, $registrationDate, $freeDate): string
    {
        return $dns
            . $registrationDate . "\n"
            . 'Регистрация истекает '
            . $freeDate
            . ' через '
            . $freeDate->diffInDays(Carbon::now()) . ' дней';
    }

    /**
     * @param $text
     * @param $project
     * @return Carbon
     */
    public static function checkDate($text, $project): Carbon
    {
        preg_match('/(free-date:)(\s\s\s\s\s)(.*)(\n)/', $text, $matches, PREG_OFFSET_CAPTURE);
        $freeDate = new Carbon($matches[3][0]);
        $countDays = $freeDate->diffInDays(Carbon::now());
        if ($countDays <= 10) {
            $project->broken = true;
        } else {
            $project->broken = false;
        }

        return $freeDate;
    }

    /**
     * @param $project
     * @param $oldState
     */
    public static function sendNotification($project, $oldState)
    {
        $user = User::find($project->user_id);
        if ($user->telegram_bot_active) {
            if ($project->broken != $oldState) {
                TelegramBot::prepareDomainInformationMessage($project, $user->chat_id);
            }
        }
    }
}
