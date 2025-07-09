<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\UserNotInProjectException;
use App\Models\Task;
use App\Models\User;

final class AssignTaskAction
{
    public function handle(Task $task, ?User $user): Task
    {
        if ($user === null) {
            $task->assignedTo()->dissociate();
        } else {
            $isUserInProject = $task->project->users()->where('users.id', $user->id)->exists();

            if (! $isUserInProject) {
                throw new UserNotInProjectException;
            }

            $task->assignedTo()->associate($user);
        }

        $task->save();

        $task->refresh();

        return $task;
    }
}
