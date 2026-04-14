<?php

namespace App\Services\deepseek\prompts;

class PromptService 
{

    public function generateAnnouncement($keywords = [], $stopwords = [], $note = null) {
        $addWords = '';
        foreach ($keywords as $item) {
            $addWords .= "<b>Нужно добавить слово " . $item['word'] . " или любое его склонение, число, падеж " . $item['count'] . " раз(а), чтобы слово естественно вписывалось в текст.</b>\n";
        }

        $cancelWords = '';
        foreach ($stopwords as $word) {
            $cancelWords .= "<b>Запрещено использовать слово и любое его склонение, число или падеж $word.</b>\n";
        }

        $text = "Роль: Ты — профессиональный копирайтер.

Исходные данные:
Ниже представлен текст о товаре, на основе которого нужно составить преимущества:
$note

Задача:
Составь список коротких тезисов, описывающих ключевые преимущества. Не выдумывай характеристики, которых нет в тексте.

Обязательные требования:
$addWords
Если ты не можешь вписать в текст слово, пропусти его. 
$cancelWords
Формат:
- Маркированный список.
- Объем до 400 символов с пробелами.
- Уникальный стиль (не копировать фразы дословно).
- Текст должен быть полностью уникальным (не скопирован с других сайтов).
- Предложения должны быть грамотными, правильными с точки зрения русского языка и легко читаться.";

        return $this->confirmPrompt($text);
    }

    public function adaptivePrompt($link, $keywords = [], $stopwords = [], $note = null, $baseText): string
    {
        $baseText = str_replace('{link}', $link, $baseText);

        $addWords = '';
        if (!empty($keywords)) {
            $addWords = "\n\nДобавь слова из списка №1 (обязательно):\n";
            foreach ($keywords as $item) {
                $addWords .= "- " . $item['word'] . " (использовать " . $item['count'] . " раз, можно склонять или менять падеж)\n";
            }
        }

        $cancelWords = '';
        if (!empty($stopwords)) {
            $cancelWords = "\nСписок №2 (запрещенные слова):\n";
            foreach ($stopwords as $word) {
                $cancelWords .= "- $word\n";
            }
        }

        $text = $baseText . $addWords . $cancelWords;

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