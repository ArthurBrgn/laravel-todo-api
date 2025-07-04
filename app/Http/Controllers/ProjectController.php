<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\ProjectException;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class ProjectController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return Project::simplePaginate()
            ->toResourceCollection();
    }

    public function tasks(Project $project): JsonResponse
    {
        $tasks = $project->tasks()
            ->with(['createdBy', 'assignedTo', 'subTasks', 'subTasks.createdBy', 'subTasks.assignedTo'])
            ->get()
            ->groupBy('status')
            ->map(fn ($group) => $group->toResourceCollection());

        return response()->json($tasks);
    }

    public function associateUser(Project $project, User $user): UserResource|JsonResponse
    {
        $isUserAlreadyPresent = $project->users()->where('users.id', $user->id)->exists();

        if ($isUserAlreadyPresent) {
            throw new ProjectException('Cet utilisateur est déjà présent dans le projet.', Response::HTTP_CONFLICT);
        }

        $project->users()->attach($user);

        return $user->toResource();
    }
}
