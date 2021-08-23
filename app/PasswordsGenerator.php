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
}
