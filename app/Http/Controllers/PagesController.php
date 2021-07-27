<?php

namespace App\Http\Controllers;

use App\HttpHeader;
use Illuminate\Http\Request;

use App\Classes\Curl\CurlFacade;
class PagesController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function httpHeaders(Request $request, HttpHeader $header)
    {
        $response = (new CurlFacade($request->input('url')))->run();
        $id = $header->saveData($response);

        return view('pages.headers', compact('response', 'id'));
    }

    /**
     * Keyword generator
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function keywordGenerator()
    {
        return view('pages.keyword');
    }

    /**
     * Word duplicates
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function duplicates()
    {
        return view('pages.duplicates');
    }

    /**
     * Generator UTM Marks
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function utmMarks()
    {
        return view('pages.utm');
    }

    /**
     * ROI Calculator
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function roiCalculator()
    {
        return view('pages.roi');
    }
}
