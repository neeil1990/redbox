<?php

namespace App\Http\Controllers;

use App\GeneratorPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class PasswordGeneratorController extends Controller
{
    public function createPassword(Request $request)
    {
        if (GeneratorPasswords::isErrors($request)) {
            Session::flash('message', 'Такая комбинация параметров недопустима');
            return Redirect::back();
        }

        if (isset($request->savePassword)) {
            $user_password = new GeneratorPasswords();
            $user_password->password = GeneratorPasswords::generatePassword($request);
            $user_password->user_id = Auth::user()->id;
            $user_password->save();
        } else {
            Session::flash('password', GeneratorPasswords::generatePassword($request));
            return Redirect::back();
        }
        return Redirect::back();
    }
}
