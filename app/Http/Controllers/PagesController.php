<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{

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
