<?php

namespace App\Http\Controllers;

use App\Text;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CountingTextLengthController extends Controller
{

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function countingTextLength(Request $request): RedirectResponse
    {
        $length = Text::countingTextLength($request->text);
        $countSpaces = Text::countingSpaces($request->text);
        $lengthWithOutSpaces = $length - $countSpaces;
        Session::flash('text', $request->text);
        Session::flash('length', $length);
        Session::flash('countSpaces', $countSpaces);
        Session::flash('lengthWithOutSpaces', $lengthWithOutSpaces);
        Session::flash('countWord', Text::countingWord($request->text));

        return Redirect::back();
    }

}
