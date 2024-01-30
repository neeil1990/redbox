<?php

namespace App\Classes\Cron\checklist;

use App\ChecklistTasks;
use Carbon\Carbon;

class ActivateTasks
{
    public function __invoke()
    {
        ChecklistTasks::where('active_after', '<', Carbon::now())->where('status', 'deactivated')
            ->update([
                'status' => 'in_work'
            ]);
    }
}
