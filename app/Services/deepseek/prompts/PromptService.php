<?php

namespace App\Services\deepseek\prompts;

use Illuminate\Support\Facades\Log;

class PromptService {

    public function generateCategory($link, $keywords = [], $stopwords = [], $note = null): string
    {
        $addWords = '';
        foreach ($keywords as $item) {
            $addWords .= "<b>Нужно добавить слово " . $item['word'] . " или любое его склонение, число, падеж " . $item['count'] . " раз(а), чтобы слово естественно вписывалось в текст.</b>\n";
        }

        $cancelWords = '';
        foreach ($stopwords as $word) {
            $cancelWords .= "<b>Запрещено использовать слово и любое его склонение, число или падеж $word.</b>\n";
        }

        $text = "Роль:
Ты — профессиональный копирайтер.

Задача:
Составь уникальный текст для категории товаров, которая расположена по ссылке: $link. 

Текст должен быть составлен таким образом, чтобы его можно было разместить на сайте в качестве SEO-текста для привлечения клиентов. Достаточно составить один вариант текста. 
Ты обязан выполнить следующие требования:
$addWords
Если ты не можешь вписать в текст слово, пропусти его. 
$cancelWords
Уникальность и грамотность:
Текст должен быть полностью уникальным (не скопирован с других сайтов).
Предложения должны быть грамотными, правильными с точки зрения русского языка и легко читаться.";

        if ($note) {
            $text .= "\nДополнительные требования к тексту: $note\n";
        }

        return $this->confirmPrompt($text);

    }

    public function regenerateCategory($currentPrompt, $currentText, $note): string
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