<?php

namespace App\Http\Controllers;

use App\UniqueWords\ShinglesWord;
use Illuminate\Http\Request;
use App\UniqueWords\WordForms;
use App\Helpers\WordHelper;

class UniqueController extends Controller
{
    public function index()
    {
        return view('unique.index');
    }

    public function dataTableView(Request $request)
    {
        $data = [];
        $content = $request->input("content", "");

        if ($content) {
            $morphy = new WordForms($content);
            $shingles = new ShinglesWord;
            $shingles->setText($content);

            foreach ($morphy->getOriginWords() as $word) {
                $forms = $morphy->getWordFormsInText($word);

                if (!$forms) {
                    continue;
                }

                $keysWords = implode(PHP_EOL, $shingles->getShinglesAroundWord($forms));
                $word = mb_strtolower($word);
                $forms = mb_strtolower(implode(', ', $forms));
                $count = $morphy->getCount();

                $data[] = [$word, $forms, $count, $keysWords];
            }
        }

        return $data;
    }

}
