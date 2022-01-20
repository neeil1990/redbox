<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ListComparisonController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:List comparison']);
    }

    /**
     * @return View
     */
    public function index(): View
    {
        return view('comparison.comparison');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listComparison(Request $request): JsonResponse
    {
        $firstList = self::removeExtraSymbols($request->firstList);
        $secondList = self::removeExtraSymbols($request->secondList);
        $result = implode("\n", self::uniquePhrases(
            explode(PHP_EOL, $firstList),
            explode(PHP_EOL, $secondList),
            $request->option
        ));

        $data = [
            'result' => $result
        ];

        return response()->json(['data' => $data]);
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

    /**
     * @param $text
     * @return array|string|string[]
     */
    public static function removeExtraSymbols($text)
    {
        return str_replace(["\r", "\n", "\r\n", "\n*"], PHP_EOL, $text);
    }
}
