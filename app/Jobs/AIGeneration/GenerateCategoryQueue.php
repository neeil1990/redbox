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

class GenerateCategoryQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(AiGenerationHistory $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $deepseekService = app(\App\Services\deepseek\DeepSeekBaseService::class);

        try {
            $finalPrompt = $this->data->prompt;
            $link = $this->data->parrameters['link'];

            if ($link) {
                $htmlContent = TextAnalyzer::removeStylesAndScripts(
                    TextAnalyzer::curlInitV2($link)
                );

                $finalPrompt .= "\n\nНиже приведено содержимое страницы. Используй его для выполнения задачи:\n";
                $finalPrompt .= "=== НАЧАЛО КОНТЕНТА ===\n";
                $finalPrompt .= $htmlContent;
                $finalPrompt .= "\n=== КОНЕЦ КОНТЕНТА ===\n";
            }

            $this->data->result = $deepseekService->request($finalPrompt);
            $this->data->status = AiGenerationHistory::COMPLETED;
            $this->data->save();
        } catch (Exception $e) {
            $this->data->result = $e->getMessage();
            $this->data->status = AiGenerationHistory::FAILED;
            $this->data->save();
        }
    }
}
