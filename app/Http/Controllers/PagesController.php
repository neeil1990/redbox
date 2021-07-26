<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Classes\Curl\CurlFacade;
class PagesController extends Controller
{

    public function httpHeaders(Request $request)
    {
        $response = (new CurlFacade($request->input('url')))->run();
        return view('pages.headers', compact('response'));
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
