<?php

namespace App\Http\Controllers;

use App\GeneratorPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PasswordGeneratorController extends Controller
{
    public function createPassword(Request $request)
    {
        GeneratorPasswords::CheckingForErrors($request);

        if (isset($request->savePassword)) {
            $user_password = new GeneratorPasswords();
            $user_password->password = GeneratorPasswords::generatePassword($request);
            $user_password->user_id = Auth::user()->id;
            $user_password->save();
        } else {
            return redirect()->back()->with('message', GeneratorPasswords::generatePassword($request));
        }
        return Redirect::back();
    }
}
