<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dtos\GetProjectTasksDto;
use App\Exceptions\UserAlreadyInProjectException;
use App\Http\Requests\GetProjectTasksRequest;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
use App\Queries\GetProjectTasksQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class ProjectController extends Controller
{
    public function index(): ResourceCollection
    {
        return Project::simplePaginate()
            ->toResourceCollection();
    }

    public function tasks(Project $project, GetProjectTasksRequest $request): JsonResponse
    {
        $tasksDto = GetProjectTasksDto::fromRequest($request);

        $query = GetProjectTasksQuery::handle($project->tasks()->getQuery(), $tasksDto);

        $tasks = $query
            ->get()
            ->groupBy('status')
            ->map(fn ($group) => $group->toResourceCollection());

        return response()->json($tasks);
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
