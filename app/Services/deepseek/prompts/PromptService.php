<?php

namespace App\Services\deepseek\prompts;

class PromptService 
{

    public function adaptivePrompt($link, $note = null, $baseText): string
    {
        $text = str_replace('{link}', $link, $baseText);

        if ($note) {
            $text .= "\nДополнительное примечание: $note\n";
        }

        return $this->confirmPrompt($text);
    }

    public function regenerateAdaptivePrompt($currentPrompt, $currentText, $note): string
    {
        $text = "Вот задача которую я тебе дал: $currentPrompt\n\n";
        $text .= "Вот текст который ты мне сгенерировал: $currentText\n\n";
        $text .= "Мне хотелось бы, чтобы ты перегенерировал текст, учитывая следующие требования: $note\n\n";

        return $this->confirmPrompt($text);
    }

    protected function confirmPrompt($prompt) {
        return $prompt . 'Никак не комментируй и не поясняй поставленную задачу, просто дай мне ответ.';
    }

}