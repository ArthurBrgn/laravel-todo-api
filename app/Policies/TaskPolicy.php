<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

final class TaskPolicy
{
    /**
     * Determine whether the user can interact with the task
     */
    public function interactWith(User $user, Task $task): bool
    {
        return $task->project->users()->where('users.id', $user->id)->exists();
    }
}
