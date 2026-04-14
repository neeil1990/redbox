<?php

namespace App\Http\Controllers;

use App\AiGenerationHistory;
use App\Jobs\AIGeneration\GenerationAnnouncementQueue;
use App\Jobs\AIGeneration\GenerationCategoryQueue;
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
        $isAllHistory = request()->is('*/all-history');

        return view('ai-generation.story', compact('isAllHistory'));
    }

    public function getHistoryJson(Request $request)
    {
        $isAllHistory = $request->input('scope') === 'all';
        
        $query = AiGenerationHistory::query();

        if (!$isAllHistory) {
            $query->where('user_id', Auth::id());
        } else {
            $query->with('user');
        }

        if ($searchValue = $request->input('search.value')) {
            $query->where(function($q) use ($searchValue) {
                $q->where('prompt', 'LIKE', "%{$searchValue}%")
                ->orWhere('parrameters', 'LIKE', "%{$searchValue}%")
                ->orWhereHas('user', function($userQuery) use ($searchValue) {
                    $userQuery->where('email', 'LIKE', "%{$searchValue}%");
                });
            });
        }

        $totalData = $query->count();
        
        $limit = $request->input('length');
        $start = $request->input('start');
        
        $items = $query->orderBy('created_at', 'desc')
                    ->offset($start)
                    ->limit($limit)
                    ->get();

        $data = $items->map(function($item) use ($isAllHistory) {
            return [
                'id'           => $item->id,
                'user_info'    => $isAllHistory ? [
                    'id'    => $item->user->id ?? '?',
                    'name'  => $item->user->name ?? '?',
                    'email' => $item->user->email ?? ''
                ] : null,
                'used_tokens'  => $item->used_tokens,
                'source'       => $item->parrameters['source'] ?? '',
                'status'       => $item->status,
                'date'         => $item->created_at->format('d.m.Y H:i'),
                'prompt'       => $item->prompt,
                'result'       => $item->result,
                'keywords'     => $item->parrameters['keywords'] ?? [],
                'stopwords'    => $item->parrameters['stopwords'] ?? [],
                'link'         => $item->parrameters['link'] ?? '',
            ];
        });

        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalData,
            "data"            => $data
        ]);
    }

    public function category()
    {
        return view('ai-generation.types.adaptive');
    }

    public function announcement()
    {
        return view('ai-generation.types.announcement');
    }

    public function getProjects()
    {
        $projects = ProjectRelevanceHistory::where('user_id', Auth::id())
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($projects);
    }

    public function generateCategory(Request $request)
    {
        $data = $request->validate([
            'id'           => 'nullable|integer',
            'link'         => 'required|url',
            'keywords'     => 'array',
            'stopwords'    => 'array',
            'note'         => 'nullable|string',
            'mode'         => 'required|string|in:new,regenerate',
            'current_text' => 'nullable|string',
            'source'       => 'required|string|in:parse_html,ai_database',
            'prompt'       => 'required|string',
        ]);

        $service = app(\App\Services\deepseek\prompts\PromptService::class);
        $prompt = '';

        if ($data['mode'] == 'new') {
            $prompt = $service->adaptivePrompt(
                $data['link'],
                $data['keywords'] ?? [],
                $data['stopwords'] ?? [],
                $data['note'] ?? null,
                $data['prompt']
            );
        } else {
            $record = AiGenerationHistory::where('user_id', Auth::id())
                ->where('id', $data['id'])
                ->where('status', AiGenerationHistory::COMPLETED)
                ->first();

            if ($record) {
                $prompt = $service->regenerateAdaptivePrompt(
                    $record->prompt,
                    $data['current_text'] ?? '',
                    $data['note'] ?? null
                );
            }
        }

        $record = AiGenerationHistory::create([
            'user_id'     => Auth::id(),
            'parrameters' => $data,
            'prompt'      => $prompt,
            'type'        => AiGenerationHistory::TYPE_CATEGORY,
            'status'      => AiGenerationHistory::PENDING,
        ]);

        GenerationCategoryQueue::dispatch($record)->onQueue('ai_generation');

        return response()->json([
            'status'    => 'ok',
            'record_id' => $record->id,
        ]);
    }

    public function generateAnnouncement(Request $request)
    {
        $data = $request->validate([
            'keywords' => 'array',
            'stopwords' => 'array',
            'current_text' => 'required|string',
        ]);

        $service = app(\App\Services\deepseek\prompts\PromptService::class);

        $prompt = $service->generateAnnouncement(
            $data['keywords'] ?? [],
            $data['stopwords'] ?? [],
            $data['current_text'],
        );

        $record = AiGenerationHistory::create([
            'user_id' => Auth::id(),
            'parrameters' => $data,
            'prompt' => $prompt,
            'type' => AiGenerationHistory::TYPE_ANNOUNCEMENT,
        ]);

        GenerationAnnouncementQueue::dispatch($record)->onQueue('ai_generation');

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
        return RelevanceHistory::where('id', $projectId)
            ->select('id', 'phrase', 'main_link', 'created_at')
            ->get();
    }

    //не могу это победить.....
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

    public function allHistory()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if(!$user::isUserAdmin()) {
            abort(403);
        }

        $generationHistory = AiGenerationHistory::orderBy('created_at', 'desc')->get();

        return view('ai-generation.story', compact('generationHistory'));
    }
}
