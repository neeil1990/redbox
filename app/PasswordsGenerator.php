<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * string $passwords
 * integer $user_id
 * @property string password
 * @property string user_id
 */
class PasswordsGenerator extends Model
{
    protected $table = 'generator_passwords';

    protected $guarded = [];

    /**
     * @param array $request
     * @return string
     */
    public static function generatePassword(array $request): string
    {
        $password = '';
        $enums = [
            0, 1, 2, 3, 4, 5, 6, 7, 8, 9
        ];
        $symbols = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z'
        ];
        $specialSymbols = [
            '%', '*', ')', '?', '@', '#', '$', '~'
        ];

        $i = 0;
        while ($i < $request['countSymbols']) {
            if (isset($request['enums'])) {
                $password .= $enums[rand(0, count($enums) - 1)];
                $i++;
            }
            if (isset($request['lowerCase'])) {
                if ($i < $request['countSymbols']) {
                    $password .= $symbols[rand(0, count($symbols) - 1)];
                    $i++;
                }
            }
            if (isset($request['upperCase'])) {
                if ($i < $request['countSymbols']) {
                    $password .= strtoupper($symbols[rand(0, count($symbols) - 1)]);
                    $i++;
                }
            }
            if (isset($request['specialSymbols'])) {
                if ($i < $request['countSymbols']) {
                    $password .= $specialSymbols[rand(0, count($specialSymbols) - 1)];
                    $i++;
                }
            }
        }

        return str_shuffle($password);
    }

    /**
     * @param $request
     * @return bool
     */
    public static function isErrors($request): bool
    {
        if (empty($request['specialSymbols']) &&
            empty($request['countSymbols']) &&
            empty($request['lowerCase']) &&
            empty($request['upperCase']) &&
            empty($request['enums'])
        ) {
            return true;
        }

        if (empty($request['specialSymbols']) &&
            empty($request['lowerCase']) &&
            empty($request['upperCase']) &&
            empty($request['enums'])
        ) {
            return true;
        }

        if (empty($request['countSymbols'])) {
            return true;
        }

        return false;
    }
}
