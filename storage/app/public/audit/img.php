<?php
$image = imagecreatetruecolor(1, 1);
imagefill($image, 0, 0, 0xFFFFFF);
header('Content-type: image/png');
imagepng($image);
imagedestroy($image);

if (isset($_GET['em'])) {
    $email = base64_decode(str_replace('0000', '=', $_GET['em'])); // получили email пользователя, который открыл письмо
    $arrayRows = file("statistics.txt"); // открываем файл для чтения, тут каждая строка будет элементом массива
    $arrayFileName = []; // массив для храниения email, полученных из файла
    foreach ($arrayRows as $k => $oneRow) {
        $a = explode('::', $oneRow); // разбираем строку на массив
        $arrayFileName[$k] = $a[0]; // получаем email из строки
    }
    if (in_array($email, $arrayFileName)) { // проверяем есть ли среди уже записанных email, наш email
        // если есть такой email, то получаем количество его скачавний и увеличиваем на 1
        $pos = 0;
        foreach ($arrayFileName as $one) {
            if ($one == $email) {
                $oldStr = $arrayRows[$pos]; // получаем старую строку
                $exp = explode('::', $arrayRows[$pos]);
                $countDownload = $exp[1]; // получаем количесво скачиваний файла
                $arrayRows[$pos] = $email . '::' . ($countDownload + 1); // увеличиваем кол-во просмотров и записываем в массив со всей статистикой
            }
            $pos++;
        }
    } else {
        // если email попался первый раз
        $arrayRows[] = $email . '::1';
    }

    $file = fopen("statistics.txt", "r+"); // открываем файл для записи
    foreach ($arrayRows as $one) { // перебераем весь массив
        fputs($file, trim($one)); // и записваем статистику
        fputs($file, "\n"); // после каждого файла делаем перевод каретки на новую строку
    }
    fclose($file);
}
