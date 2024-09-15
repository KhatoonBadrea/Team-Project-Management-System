<?php

namespace App\Listeners;

use App\Events\TaskStatusUpdatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateDueDateListener
{
    /**
     * Handle the event.
     */
    public function handle(TaskStatusUpdatedEvent $event): void
    {
        $task = $event->task;

        // إذا كانت الحالة "in-progress"، قم بتسجيل وقت البدء
        if ($task->status === 'in-progress') {
            $task->start_time = now();
            $task->save();
        }

        // إذا كانت الحالة "done"، قم بتسجيل وقت الانتهاء واحسب عدد الساعات
        if ($task->status === 'done') {
            $task->end_time = now();

            // حساب عدد الساعات بين وقت البدء والانتهاء
            $hoursSpent = Carbon::parse($task->start_time)->diffInHours($task->end_time);

            // تحديث الساعات في جدول pivot
            DB::table('project_user')
                ->where('project_id', $task->project_id)
                ->where('user_id', $task->assigned_to)
                ->increment('num_of_hours', $hoursSpent); // إضافة الساعات المحسوبة

            // تحديث تاريخ الاستحقاق في المهمة
            $task->due_date = now();
            $task->save();
        }
    }
}
