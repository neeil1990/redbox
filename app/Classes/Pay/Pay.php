<?php


namespace App\Classes\Pay;


abstract class Pay
{
    protected $login;
    protected $password;
    protected $password2;

    /**
     * @return mixed
     */
    public function getPassword2()
    {
        return $this->password2;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
