<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;
use App\Models\User;

final class AssignTaskAction
{
    public function handle(Task $task, User $user): Task
    {
        $task->assignedTo()->associate($user);

        $task->save();

        $task->refresh();

        return $task;
    }
}
