<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;

final class AssignTaskAction
{
    public function handle(Task $task, User $user): Task
    {
        $task->assignedTo()->associate($user);

        $task->save();

        $task->refresh();

        $user->notify(new TaskAssignedNotification($task));

        return $task;
    }
}
