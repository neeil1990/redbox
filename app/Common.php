<?php

namespace App;

class Common
{
    /**
     * @param $file
     * @param string $type
     * @param string $name
     * @return void
     */
    public static function fileExport($file, string $type, string $name = '')
    {
        if (!$name) {
            $name = md5(microtime());
        }

        $fileName = $file->getFile()->getFilename();

        $filePath = storage_path('framework/laravel-excel/' . $fileName);
        $newFileName = storage_path('framework/laravel-excel/' . $name) . '.' . $type;

        rename($filePath, $newFileName);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($newFileName));
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($newFileName));

        readfile($newFileName);

        unlink($newFileName);
    }
}
