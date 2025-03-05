<?php

namespace App\Http\Controllers;

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

            foreach ($morphy->getOriginWords() as $word) {
                $forms = $morphy->getWordFormsInText($word);

                if (!$forms) {
                    continue;
                }

                $data[] = [$word, mb_strtolower(implode(', ', $forms)), $morphy->getCount(), ""];
            }
        }

        return $data;
    }

}
