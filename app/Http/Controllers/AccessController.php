<?php

namespace App\Http\Controllers;


class AccessController extends Controller
{

    public function getPersonalData()
    {
        return view('users.personal-data');
    }

    public function getPrivacyPolicy()
    {
        return view('users.privacy-policy');
    }
}
