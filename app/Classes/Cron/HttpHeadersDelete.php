<?php


namespace App\Classes\Cron;


use App\HttpHeader;
use App\HttpHeadersSettings;
use Carbon\Carbon;

class HttpHeadersDelete
{
    public function __invoke()
    {
        $settings = new HttpHeadersSettings();

        $delete_records = $settings->where('code', 'delete_records')->value('value');

        if($delete_records)
            HttpHeader::where('created_at', '<', Carbon::now()->subDays($delete_records))->delete();
    }
}
