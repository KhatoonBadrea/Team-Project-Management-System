<?php

namespace App\Listeners;

use App\Events\TaskStatusUpdatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateDueDateListener
{
    /**
     * Handle the event.
     */
    public function handle(TaskStatusUpdatedEvent $event): void
    {
        $task = $event->task;
        //check if status = done 
        if ($task->status === 'done') {
            $task->due_date = now();
            $task->save();
        }
    }
}
