<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;

final class UnassignTaskAction
{
    public function handle(Task $task): Task
    {
        $task->assignedTo()->dissociate();

        $task->save();

        $task->refresh();

        return $task;
    }
}
