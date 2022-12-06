<?php
\Illuminate\Support\Facades\Log::debug('generate image pixel audit');
// генерируем картинку и отдаем ее
$image = imagecreatetruecolor(1, 1); // создаем холст 1 на 1 пиксель
imagefill($image, 0, 0, 0xFFFFFF); // делаем его белым
header('Content-type: image/png'); // задаем заголовок
imagepng($image); // выводим картинку
imagedestroy($image); // очищаем память от картинки

// ведем статистику
if (isset($_GET['em'])) {
    $email = base64_decode($_GET['em']); // получили email пользователя, который открыл письмо
    \Illuminate\Support\Facades\Log::debug('pixel-audit', [$email]);
}
