<?php


namespace App\Classes\Cron;

use App\MetaTagsHistory;
use App\MetaTagsSettings;
use Carbon\Carbon;


class MetaTagsHistoriesDelete
{
    public function __invoke()
    {
        $settings = new MetaTagsSettings();

        $delete_records = $settings->where('code', 'delete_records')->value('value');

        if($delete_records)
        {
            MetaTagsHistory::where([
                ['created_at', '<', Carbon::now()->subDays($delete_records)],
                ['ideal', '=', 0]
            ])->delete();
        }
    }

}
