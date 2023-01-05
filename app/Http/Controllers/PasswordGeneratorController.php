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
    public function __construct()
    {
        $this->middleware(['permission:Password generator']);
    }

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
        if (PasswordsGenerator::isErrors($request->all())) {
            flash()->overlay(__('This combination of parameters is not allowed'), ' ')->error();
            return Redirect::back();
        }
        if (isset($request->savePassword)) {
            $userPassword = new PasswordsGenerator();
            $userPassword->password = PasswordsGenerator::generatePassword($request->all());
            $userPassword->user_id = Auth::id();
            $userPassword->save();
        } else {
            $passwords = array();
            for ($i = 0; $i < 5; $i++) {
                $passwords[] = PasswordsGenerator::generatePassword($request->all());
            }
            return view('pages.password', ['user' => Auth::user(), 'passwords' => $passwords]);
        }
        return Redirect::route('pages.password');
    }

    public function editComment(Request $request): \Illuminate\Http\JsonResponse
    {
        PasswordsGenerator::where('id', '=', $request->input('id'))
            ->where('user_id', '=', Auth::id())
            ->update(['comment' => $request->input('comment')]);

        return response()->json([
            'success' => true
        ]);
    }

    public function remove(Request $request): \Illuminate\Http\JsonResponse
    {
        PasswordsGenerator::where('id', '=', $request->input('id'))
            ->where('user_id', '=', Auth::id())
            ->delete();

        return response()->json([
            'success' => true
        ]);
    }

}
