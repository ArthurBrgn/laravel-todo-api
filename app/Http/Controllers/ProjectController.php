<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\UserAlreadyInProjectException;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class ProjectController extends Controller
{
    public function index(): ResourceCollection
    {
        return Project::simplePaginate()
            ->toResourceCollection();
    }

    public function associateUser(Project $project, User $user): UserResource
    {
        $isUserAlreadyPresent = $project->users()
            ->where('users.id', $user->id)->exists();

        if ($isUserAlreadyPresent) {
            throw new UserAlreadyInProjectException;
        }

        $project->users()->attach($user);

        return new UserResource($user);
    }
}
