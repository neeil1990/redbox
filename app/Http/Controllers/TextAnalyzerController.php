<?php

namespace App\Http\Controllers;

use App\TariffSetting;
use App\TextAnalyzer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TextAnalyzerController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:Text analyzer']);
    }

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function index()
    {
        return view('text-analyse.index');
    }

    /**
     * @param Request $request
     * @return array|false|Application|Factory|RedirectResponse|View|mixed
     * @throws ValidationException
     */
    public function analyze(Request $request)
    {
        $this->validator($request);

        if (TariffSetting::checkTextAnalyserLimits()) {
            flash()->overlay(__('Your limits are exhausted this month'), ' ')->error();
            return Redirect::back();
        }

        $request = $request->all();
        if ($request['type'] === 'url') {
            $html = TextAnalyzer::curlInit($request['url']);
            if (!$html) {
                flash()->overlay(__('connection attempt failed'), ' ')->error();

                return Redirect::back();
            } else {
                $html = TextAnalyzer::removeStylesAndScripts($html);
                $response = TextAnalyzer::analyze($html, $request);
            }

        } else {
            $response = TextAnalyzer::analyze($request['textarea'], $request);
        }

        return view('text-analyse.index', compact('response', 'request'));
    }

    /**
     * @param $url
     * @return array|false|Application|Factory|View|mixed
     */
    public function redirectToAnalyse($url)
    {
        $url = str_replace('abc', '/', $url);

        return view('text-analyse.index', compact('url'));
    }

    /**
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    protected function validator(Request $request)
    {
        if ($request['type'] === 'text') {
            $this->validate($request, [
                'textarea' => 'required|min:200',
            ], [
                'textarea.required' => 'Вы не заполнили поле с текстом',
                'textarea.min' => 'Длинна текста минимум 200 символов',
            ]);
        } else {
            $this->validate($request, [
                'url' => 'required|website',
            ], [
                'url.required' => 'Вы не заполнили поле с URL',
                'url.website' => 'URL должен быть валидным'
            ]);
        }
    }

}
