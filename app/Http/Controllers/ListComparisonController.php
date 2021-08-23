<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ListComparisonController extends Controller
{

    /**
     * @return View
     */
    public function index(): View
    {
        return view('pages.comparison');
    }

    public function listComparison(Request $request)
    {
        Log::debug('php_eol', ["\r\n"]);
        Log::debug('sting first list', [$request->firstList]);
        Log::debug('sting second list', [$request->secondList]);
        Log::debug('explode first list', explode("\r\n", $request->firstList));
        Log::debug('explode second list', explode("\r\n", $request->secondList));

        $result = implode("\r\n", self::uniquePhrases(
            explode("\r\n", $request->firstList),
            explode("\r\n", $request->secondList),
            $request->option
        ));

        Session::flash('result', $result);
        return Redirect::back()->withInput($request->toArray());
    }

    /**
     * @param $firstList
     * @param $secondList
     * @param $position
     * @return array|void
     */
    public static function uniquePhrases($firstList, $secondList, $position): array
    {
        switch ($position) {
            case 'uniqueInFirstList':
                Log::debug('uniqueInFirstList', array_diff($firstList, $secondList));
                return array_diff($firstList, $secondList);
            case 'uniqueInSecondList':
                Log::debug('uniqueInSecondList', array_diff($secondList, $firstList));
                return array_diff($secondList, $firstList);
            case 'unique':
                Log::debug('unique', array_intersect($firstList, $secondList));
                return array_intersect($firstList, $secondList);
            case 'union':
                Log::debug('union', array_unique(array_merge($firstList, $secondList)));
                return array_unique(array_merge($firstList, $secondList));
        }
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function downloadComparisonFile(Request $request)
    {
        $fileName = md5(Carbon::now());
        Storage::put('files\\' . $fileName . '.txt', $request->result);
        return response()->download(storage_path('app\public\files\\' . $fileName . '.txt'));
    }
}
