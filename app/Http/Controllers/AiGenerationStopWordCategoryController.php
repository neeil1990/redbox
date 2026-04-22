<?php

namespace App\Http\Controllers;

use App\AiGenerationStopWordCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiGenerationStopWordCategoryController extends Controller
{
    public function index()
    {
        $categories = AiGenerationStopWordCategory::where('user_id', Auth::id())
            ->latest()
            ->get();
            
        return view('ai-generation.stopword-categories', compact('categories'));
    }

    public function datatable()
    {
        $categories = AiGenerationStopWordCategory::where('user_id', Auth::id())->latest()->get();
        return response()->json(['data' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        AiGenerationStopWordCategory::create(['user_id' => Auth::id(), 'name' => $request->name]);

        if ($request->ajax()) return response()->json(['success' => true]);
        return back()->with('success', 'Категория создана');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category = AiGenerationStopWordCategory::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $category->update(['name' => $request->name]);

        if ($request->ajax()) return response()->json(['success' => true]);
        return back()->with('success', 'Категория обновлена');
    }

    public function destroy(Request $request, $id)
    {
        AiGenerationStopWordCategory::where('id', $id)->where('user_id', Auth::id())->delete();
        
        if ($request->ajax()) return response()->json(['success' => true]);
        return back()->with('success', 'Категория удалена');
    }
}