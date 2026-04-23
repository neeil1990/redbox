<?php

namespace App\Jobs\AIGeneration;

use App\AiGenerationHistory;
use App\TextAnalyzer;
use Illuminate\Bus\Queueable;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerationCategoryQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    public function __construct(AiGenerationHistory $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        $deepseekService = app(\App\Services\deepseek\DeepSeekBaseService::class);

        try {
            $finalPrompt = $this->getMacroses($this->data->prompt);
            $finalPrompt .= $this->getWords();
            $finalPrompt .= $this->getCancelWords();

            if ($this->data->parrameters['source'] === AiGenerationHistory::SOURCE_PARSE_HTML) {
                $link = $this->data->parrameters['link'];
                $htmlContent = TextAnalyzer::removeStylesAndScripts(
                    TextAnalyzer::curlInitV2($link)
                );

                $finalPrompt .= "\n\nНиже приведено содержимое страницы. Используй его для выполнения задачи:\n";
                $finalPrompt .= "=== НАЧАЛО КОНТЕНТА ===\n";
                $finalPrompt .= $htmlContent;
                $finalPrompt .= "\n=== КОНЕЦ КОНТЕНТА ===\n";
            }

            $this->data->result = $deepseekService->request($finalPrompt);
            $this->data->used_tokens = $deepseekService->getLastUsageTokens();
            $this->data->status = AiGenerationHistory::COMPLETED;
            $this->data->save();
        } catch (Exception $e) {
            $this->data->result = $e->getMessage();
            $this->data->status = AiGenerationHistory::FAILED;
            $this->data->save();
        }
    }

    private function getMacroses($finalPrompt) {
        if (preg_match_all('/--(.+?)--/', $finalPrompt, $matches)) {
            $macroNames = array_unique($matches[1]);

            $macros = \App\AiGenerationMacro::where('user_id', $this->data->user_id)
                ->whereIn('name', $macroNames)
                ->get()
                ->keyBy('name');

            foreach ($macroNames as $macroName) {
                if ($macros->has($macroName)) {
                    $finalPrompt = str_replace(
                        '--' . $macroName . '--', 
                        $macros->get($macroName)->content, 
                        $finalPrompt
                    );
                }
            }
        }

        return $finalPrompt;
    }

    private function getWords() {
        $addWords = '';
        if (isset($this->data->parrameters['keywords'])) {
            $addWords = "\n\nДобавь каждое слова из этого списка:\n";
            foreach ($this->data->parrameters['keywords'] as $item) {
                $addWords .= "- " . $item['word'] . " (использовать " . $item['count'] . " раз, можно склонять или менять падеж)\n";
            }
        }

        return $addWords;
    }

    private function getCancelWords() {
        $cancelWords = '';
        if (isset($this->data->parrameters['stopwords'])) {
            $cancelWords = "\nСлова которые запрещенно использовать в любом числе и падеже:\n";
            foreach ($this->data->parrameters['stopwords'] as $word) {
                $cancelWords .= "- $word\n";
            }
        }

        return $cancelWords;
    }
}
