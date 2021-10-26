<?php

namespace App\Http\Controllers;

use App\PasswordsGenerator;
use Faker\Factory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PasswordGeneratorController extends Controller
{

    /**
     * @return Factory|View
     */
    public function index(): View
    {
        return view('pages.password', ['user' => Auth::user()]);
    }

    /**
     * @param Request $request
     * @return array|Application|\Illuminate\Contracts\View\Factory|RedirectResponse|View|mixed
     */
    public function createPassword(Request $request)
    {
        if ($this->isErrors($request)) {
            flash()->overlay(__('This combination of parameters is not allowed'), ' ')->error();
            return Redirect::back();
        }
        if (isset($request->savePassword)) {
            $userPassword = new PasswordsGenerator();
            $userPassword->password = $this->generatePassword($request);
            $userPassword->user_id = Auth::user()->id;
            $userPassword->save();
        } else {
            $passwords = array();
            for ($i = 0; $i < 5; $i++) {
                $passwords[] = $this->generatePassword($request);
            }
            return view('pages.password', ['user' => Auth::user(), 'passwords' => $passwords]);
        }
        return Redirect::back();
    }

    /**
     * @param $request
     * @return string
     */
    public function generatePassword($request): string
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
        while ($i < $request->countSymbols) {
            if ($request->enums) {
                $password .= $enums[rand(0, count($enums) - 1)];
                $i++;
            }
            if ($request->lowerCase) {
                if ($i < $request->countSymbols) {
                    $password .= $symbols[rand(0, count($symbols) - 1)];
                    $i++;
                }
            }
            if ($request->upperCase) {
                if ($i < $request->countSymbols) {
                    $password .= strtoupper($symbols[rand(0, count($symbols) - 1)]);
                    $i++;
                }
            }
            if ($request->specialSymbols) {
                if ($i < $request->countSymbols) {
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
    public function isErrors($request): bool
    {
        if (empty($request->specialSymbols) &&
            empty($request->countSymbols) &&
            empty($request->lowerCase) &&
            empty($request->upperCase) &&
            empty($request->enums)
        ) {
            return true;
        }

        if (empty($request->specialSymbols) &&
            empty($request->lowerCase) &&
            empty($request->upperCase) &&
            empty($request->enums)
        ) {
            return true;
        }

        if (empty($request->countSymbols)) {
            return true;
        }

        return false;
    }

}
