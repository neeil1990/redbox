<?php


namespace App\Classes\Cron;

use App\MetaTagsHistory;
use Carbon\Carbon;


class MetaTagsHistoriesDelete
{
    protected $days_ago = 90;

    public function __invoke()
    {

        MetaTagsHistory::where([
            ['created_at', '<', Carbon::now()->subDays($this->days_ago)],
            ['ideal', '=', 0]
        ])->delete();
    }

}
