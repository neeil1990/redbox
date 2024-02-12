<?php

namespace App\Classes\Cron\checklist;

use App\ChecklistNotification;
use App\ChecklistTasks;
use Carbon\Carbon;

class Notifications
{
    public function __invoke()
    {
        $tasks = ChecklistTasks::where('deadline', '<', Carbon::now())->where('status', '!=', 'expired')->get();

        foreach ($tasks as $task) {
            $task->update([
                'status' => 'expired'
            ]);

            ChecklistNotification::where('checklist_task_id', $task->id)->update(['status' => 'notification']);
        }
    }
}
