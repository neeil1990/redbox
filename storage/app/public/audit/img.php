<?php
$image = imagecreatetruecolor(1, 1);
imagefill($image, 0, 0, 0xFFFFFF);
header('Content-type: image/png');
imagepng($image);
imagedestroy($image);

if (isset($_GET['em'])) {
    $email = base64_decode(str_replace('0000', '=', $_GET['em'])); // получили email пользователя, который открыл письмо
    $connection = mysqli_connect('127.0.0.1', 'lk_redbox_su_usr', '0066FJVQ16Muz63j', 'lk_redbox_su_db');
    $query = "UPDATE `users` SET `read_letter` = true WHERE `email` = '" . $email . "';";
    $result = mysqli_query($connection, $query);
    $result = $result->fetch_assoc();
}
