<?php

namespace App\Http\Controllers;

use App\TextAnalyzer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;


class TextAnalyzerController extends Controller
{

    public function index()
    {
        return view('text-analyzer.index');
    }

    /**
     * @param Request $request
     * @return array|Application|Factory|RedirectResponse|View|mixed
     */
    public function analyze(Request $request)
    {
        if ($request->type === 'url') {
            $html = TextAnalyzer::curlInit($request->text);
            if ($html == false) {
                flash()->overlay('connection attempt failed', ' ')->error();
                return Redirect::back();
            } else {
                $html = TextAnalyzer::removeHeaders($html);
                $response = TextAnalyzer::analyze($html);
            }
        } else {
            if (strlen($request->text) > 200 && strlen($request->text) < 100000) {
                $response = TextAnalyzer::analyze($request->text);
            } else {
                flash()->overlay(__('The volume of the text should be from 200 to 100,000 characters'), ' ')->error();
                return Redirect::back();
            }
        }
        $response['text'] = $request->text;
        $response['type'] = $request->type;
        return view('text-analyzer.index', ['response' => $response]);
    }

}
