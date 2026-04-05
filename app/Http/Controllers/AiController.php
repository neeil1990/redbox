<?php

namespace App\Http\Controllers;

use App\AiGenerationHistory;
use App\Jobs\AIGeneration\GenerateCategoryQueue;
use App\ProjectRelevanceHistory;
use App\Relevance;
use App\RelevanceHistory;
use App\RelevanceHistoryResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    public function story()
    {
        $generationHistory = AiGenerationHistory::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ai-generation.story', compact('generationHistory'));
    }

    public function category()
    {
        $projects = ProjectRelevanceHistory::where('user_id', Auth::id())->orderBy('name')->get();

        return view('ai-generation.category', compact('projects'));
    }

    public function generateCategory(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|integer',
            'link' => 'required|url',
            'keywords' => 'array',
            'stopwords' => 'array',
            'note' => 'nullable|string',
            'mode' => 'required|string',
            'current_text' => 'nullable|string',
        ]);

        $service = app(\App\Services\deepseek\prompts\PromptService::class);

        if($data['mode'] == 'new') {
            $prompt = $service->generateCategory(
                $data['link'],
                $data['keywords'] ?? [],
                $data['stopwords'] ?? [],
                $data['note'] ?? null
            );
        } else {
            $record = AiGenerationHistory::where('user_id', Auth::id())
                ->where('id', $data['id'])
                ->where('status', AiGenerationHistory::COMPLETED)
                ->first();

            if($record) {
                $prompt = $service->regenerateCategory(
                    $record->prompt,
                    $data['current_text'] ?? '',
                    $data['note'] ?? null
                );
            }
        }

        $record = AiGenerationHistory::create([
            'user_id' => Auth::id(),
            'parrameters' => $data,
            'prompt' => $prompt,
            'type' => AiGenerationHistory::TYPE_CATEGORY,
        ]);

        GenerateCategoryQueue::dispatch($record)->onQueue('ai_generation');

        return response()->json([
            'status' => 'ok',
            'record_id' => $record->id,
        ]);
    }

    public function getResult($recordId)
    {
        $record = AiGenerationHistory::where('id', $recordId)
            ->whereIn('status', [AiGenerationHistory::COMPLETED, AiGenerationHistory::FAILED])
            ->where('user_id', Auth::id())
            ->first();

        return response()->json([
            'status' => 'ok',
            'record' => $record,
        ]);
    }

    public function relevanceHistory($projectId)
    {
        return RelevanceHistory::where('project_relevance_history_id', $projectId)
            ->select('id', 'phrase', 'main_link')
            ->get();
    }

    public function getPhrases($projectId) {
        $record = RelevanceHistoryResult::where('project_id', $projectId)->first();

        if($record) {
            $phrases = Relevance::uncompressItem($record->phrases);

            return response()->json([
                'status' => 'ok',
                'phrases' => $phrases,
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'phrases' => [],
        ]);
    }
}
