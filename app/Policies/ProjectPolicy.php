<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

final class ProjectPolicy
{
    /**
     * Determine whether the user can interact with the project
     */
    public function interactWith(User $user, Project $project): bool
    {
        return $project->users()->where('users.id', $user->id)->exists();
    }
}
