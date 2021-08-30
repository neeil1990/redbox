<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
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

    /**
     * @param Request $request
     * @return array|false|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|View|mixed
     */
    public function listComparison(Request $request)
    {
        $result = implode("\r\n", self::uniquePhrases(
            explode("\r\n", $request->firstList),
            explode("\r\n", $request->secondList),
            $request->option
        ));

        $firstList = $request->firstList;
        $secondList = $request->secondList;

        return view('pages.comparison', compact(
            'firstList',
            'secondList',
            'result'));
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
                return array_diff(array_unique(array_diff($firstList, $secondList)), array(""));
            case 'uniqueInSecondList':
                return array_diff(array_unique(array_diff($secondList, $firstList)), array(""));
            case 'unique':
                return array_diff(array_unique(array_intersect($firstList, $secondList)), array(""));
            case 'union':
                return array_diff(array_unique(array_merge($firstList, $secondList)), array(""));
        }
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function downloadComparisonFile(Request $request): BinaryFileResponse
    {
        $fileName = md5(Carbon::now());
        Storage::put('files\\' . $fileName . '.txt', $request->result);
        return response()->download(storage_path('app/public/files/' . $fileName . '.txt'));
    }
}
