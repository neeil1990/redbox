<?php

namespace App\Http\Controllers;

use App\AiGenerationMacro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiGenerationMacroController extends Controller
{
    public function index()
    {
        return view('ai-generation.macros');
    }

    public function datatable()
    {
        $macros = AiGenerationMacro::where('user_id', Auth::id())->latest()->get();
        return response()->json(['data' => $macros]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string'
        ]);

        AiGenerationMacro::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'content' => $request->content,
            'description' => $request->description
        ]);

        if ($request->ajax()) return response()->json(['success' => true]);
        return back()->with('success', 'Макрос создан');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $macro = AiGenerationMacro::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        $macro->update([
            'name' => $request->name,
            'content' => $request->content,
            'description' => $request->description
        ]);

        if ($request->ajax()) return response()->json(['success' => true]);
        return back()->with('success', 'Макрос обновлен');
    }

    public function destroy(Request $request, $id)
    {
        AiGenerationMacro::where('id', $id)->where('user_id', Auth::id())->delete();
        
        if ($request->ajax()) return response()->json(['success' => true]);
        return back()->with('success', 'Макрос удален');
    }
}