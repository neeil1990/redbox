<?php

namespace App\Http\Controllers;

use App\Classes\Tariffs\FreeTariff;
use App\Classes\Tariffs\OptimalTariff;
use App\HttpHeader;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Classes\Curl\CurlFacade;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HttpHeadersExport;

class PagesController extends Controller
{

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function httpHeaders(Request $request, HttpHeader $header)
    {
        if($request->input('http', false))
            return (new CurlFacade($request->input('url')))->httpCode();

        $response = (new CurlFacade($request->input('url')))->run();
        $id = $header->saveData($response);

        return view('pages.headers', compact('response', 'id'));
    }

    public function httpHeadersExport($object)
    {
        $items = json_decode(base64_decode($object), true);
        return Excel::download(new HttpHeadersExport($items), 'http_headers.csv');
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

    protected function getTariff()
    {
        $user = auth()->user();

        if($user->hasRole('Free'))
            $tariff = (new FreeTariff())->get();
        elseif($user->hasRole('Optimal'))
            $tariff = (new OptimalTariff())->get();

        return (isset($tariff)) ? $tariff : null;
    }

    /**
     * Word duplicates
     *
     * @return Factory|View
     */
    public function duplicates($quantity = 0)
    {

        $tariff = $this->getTariff();
        $require = $tariff['settings']['duplicates_str_length'];

        if(isset($require) && $quantity > $require)
            return collect(['require' => $require, 'quantity' => $quantity]);

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
