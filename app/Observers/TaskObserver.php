<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    public function updated(Task $task)
    {
        dd("masuk");
        if ($task->creator_id !== auth('sanctum')->user()->id) {
            return "Cannot update task created by another user.";
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
