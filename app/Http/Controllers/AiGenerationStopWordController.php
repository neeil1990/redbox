<?php

namespace App\Http\Controllers;

use App\AiGenerationStopWords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiGenerationStopWordController extends Controller
{
    public function index()
    {
        $words = AiGenerationStopWords::where('user_id', Auth::id())->latest()->get();
        return view('ai-generation.stopwords', compact('words'));
    }

    public function store(Request $request)
    {
        $request->validate(['word' => 'required|string|max:255']);

        AiGenerationStopWords::create([
            'user_id' => Auth::id(),
            'word' => $request->word
        ]);

        return back()->with('success', 'Слово добавлено');
    }

    public function destroy($id)
    {
        AiGenerationStopWords::where('id', $id)->where('user_id', Auth::id())->delete();
        return back()->with('success', 'Слово удалено');
    }

    public function getJson()
    {
        $words = AiGenerationStopWords::where('user_id', Auth::id())->pluck('word');
        return response()->json($words);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['word' => 'required|string|max:255']);

        $word = AiGenerationStopWords::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $word->update(['word' => $request->word]);

        return back()->with('success', 'Слово обновлено');
    }
}