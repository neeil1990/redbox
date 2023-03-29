<?php


namespace App\Exports\Monitoring\Format;


interface IFormat
{
    public function download($data, $fileName);
}
