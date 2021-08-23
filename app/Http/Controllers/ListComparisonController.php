<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $result = implode(PHP_EOL, self::uniquePhrases(
            explode(PHP_EOL, $request->firstList),
            explode(PHP_EOL, $request->secondList),
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
                return array_diff($firstList, $secondList);
            case 'uniqueInSecondList':
                return array_diff($secondList, $firstList);
            case 'unique':
                return array_intersect($firstList, $secondList);
            case 'union':
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
