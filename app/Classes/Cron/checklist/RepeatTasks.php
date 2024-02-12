<?php

namespace App\Classes\Cron\checklist;

use App\ChecklistTasks;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RepeatTasks
{
    public function __invoke()
    {
        $tasks = ChecklistTasks::where('status', 'repeat')
            ->where('date_start', '<', Carbon::now())
            ->get();

        foreach ($tasks as $task) {
            try {
                DB::beginTransaction();
                $newTask = new ChecklistTasks([
                    'project_id' => $task->id,
                    'name' => $task->name,
                    'status' => 'new',
                    'description' => $task->description,
                    'date_start' => $task->date_start,
                    'deadline' => Carbon::parse($task->date_start)->addDays($task->deadline_every)
                ]);
                $newTask->save();

                if ($task['weekends']) {
                    $task->update([
                        'date_start' => Carbon::parse($task->date_start)->addWeekdays($task->repeat_every)
                    ]);
                } else {
                    $task->update([
                        'date_start' => Carbon::parse($task->date_start)->addDays($task->repeat_every)
                    ]);
                }
                DB::commit();
            } catch (\Throwable $e) {
                Log::debug('error', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                DB::rollback();
            }

        }
    }
}
