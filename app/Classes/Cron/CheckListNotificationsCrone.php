<?php

namespace App\Classes\Cron;

use App\ChecklistNotification;
use App\ChecklistTasks;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckListNotificationsCrone
{
    public function __invoke()
    {
        Log::info('Check checklist notifications');
        $tasks = ChecklistTasks::where('deadline', '<', Carbon::now())
            ->where('status', 'in_work')
            ->get();

        foreach ($tasks as $task) {
            $task->update([
                'status' => 'expired'
            ]);

            ChecklistNotification::where('checklist_task_id', $task->id)
                ->update([
                    'status' => 'notification'
                ]);
        }
    }
}
