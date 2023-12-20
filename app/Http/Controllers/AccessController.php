<?php

namespace App\Http\Controllers;


use App\PolicyTermsDocs;

class AccessController extends Controller
{

    public function getRuPersonalData()
    {
        $doc = PolicyTermsDocs::first();

        return view('users.docs.personal-data', ['doc' => $doc->policy_ru]);
    }

    public function getEnPersonalData()
    {
        $doc = PolicyTermsDocs::first();

        return view('users.docs.personal-data', ['doc' => $doc->policy_en]);
    }

    public function getRuPrivacyPolicy()
    {
        $doc = PolicyTermsDocs::first();

        return view('users.docs.privacy-policy', ['doc' => $doc->terms_ru]);
    }

    public function getEnPrivacyPolicy()
    {
        $doc = PolicyTermsDocs::first();

        return view('users.docs.privacy-policy', ['doc' => $doc->terms_en]);
    }
}
