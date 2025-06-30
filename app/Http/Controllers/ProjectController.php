<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProjectController extends Controller
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

    public function addUser(Project $project, User $user): UserResource|JsonResponse
    {

        $isUserAlreadyPresent = $project->users()->where('users.id', $user->id)->exists();

        if ($isUserAlreadyPresent) {
            return response()->json([], Response::HTTP_CONFLICT);
        }

        $project->users()->attach($user);

        return $user->toResource();
    }
}
