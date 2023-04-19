<?php

namespace App\Http\Controllers;

use App\HttpHeader;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Classes\Curl\CurlFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class PagesController extends Controller
{
    /**
     * @param Request $request
     * @param HttpHeader $header
     * @return array|false|\Illuminate\Contracts\Foundation\Application|Factory|View|mixed
     */
    public function httpHeaders(Request $request, HttpHeader $header)
    {
        $lang = $header->lang;
        $user = Auth::user();
        if($user)
            $lang = $user['lang'];

        if($request->input('http', false))
            return (new CurlFacade($request->input('url')))->httpCode();

        $response = (new CurlFacade($request->input('url')))->run();
        $id = $header->saveData($response);

        return view('pages.headers', compact('response', 'id', 'lang'));
    }

    /**
     * Keyword generator
     *
     * @return Factory|View
     */
    public function keywordGenerator()
    {
        return view('pages.keyword');
    }

    /**
     * Word duplicates
     *
     * @return Factory|View
     */
    public function duplicates()
    {
        $options = collect([
            1 => __('remove duplicate spaces between words'),
            2 => __('remove spaces and tabs at the beginning and end of the line'),
            3 => __('replace tabs with spaces'),
            4 => __('remove blank lines'),
            5 => __('convert to lowercase'),
            6 => __('remove characters at the beginning of a word'),
            7 => __('remove characters at the end of a word'),
            8 => __('remove duplicates'),
            9 => __('replace'),
        ])->toJson();

        return view('pages.duplicates', compact('options'));
    }

    /**
     * Generator UTM Marks
     *
     * @return Factory|View
     */
    public function utmMarks()
    {
        return view('pages.utm');
    }

    /**
     * ROI Calculator
     *
     * @return Factory|View
     */
    public function roiCalculator()
    {
        return view('pages.roi');
    }
}
