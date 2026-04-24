<?php

namespace App\Http\Controllers;

use App\AiGenerationStopWords;
use App\AiGenerationStopWordCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiGenerationStopWordController extends Controller
{
public function index()
    {
        $words = AiGenerationStopWords::with('category')->where('user_id', Auth::id())->latest()->get();
        $categories = AiGenerationStopWordCategory::where('user_id', Auth::id())->orderBy('name')->get();

        return view('ai-generation.stopwords', compact('words', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:255',
            'category_id' => 'nullable|exists:ai_generation_stop_word_categories,id'
        ]);

        AiGenerationStopWords::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'word' => $request->word
        ]);

        if ($request->ajax()) return response()->json(['success' => true]);
        return back()->with('success', 'Слово добавлено');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'word' => 'required|string|max:255',
            'category_id' => 'nullable|exists:ai_generation_stop_word_categories,id'
        ]);

        $word = AiGenerationStopWords::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $word->update(['word' => $request->word, 'category_id' => $request->category_id]);

        if ($request->ajax()) return response()->json(['success' => true]);
        return back()->with('success', 'Слово обновлено');
    }

    public function destroy(Request $request, $id)
    {
        AiGenerationStopWords::where('id', $id)->where('user_id', Auth::id())->delete();
        
        if ($request->ajax()) return response()->json(['success' => true]);
        return back()->with('success', 'Слово удалено');
    }

    public function getJson(Request $request)
    {
        $categoryId = $request->input('category', 'all');

        $query = AiGenerationStopWords::with('category')
            ->where('user_id', Auth::id());

        if($categoryId === 'null') {
            $query->whereNull('category_id');
        } else if ($categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        $words = $query->get();

        $grouped = $words->groupBy(function ($item) {
            return $item->category ? $item->category->name : 'Без категории';
        })->map(function ($group) {
            return $group->pluck('word');
        });

        return response()->json($grouped);
    }

    public function datatable()
    {
        $words = AiGenerationStopWords::with('category')->where('user_id', Auth::id())->latest()->get();
        return response()->json(['data' => $words]);
    }
}