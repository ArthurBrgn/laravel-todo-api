<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateProjectAction;
use App\Dtos\CreateProjectDto;
use App\Exceptions\UserAlreadyInProjectException;
use App\Http\Requests\CreateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

final class ProjectController extends Controller
{
    public function index(): ResourceCollection
    {
        return Project::simplePaginate()
            ->toResourceCollection();
    }

    public function create(CreateProjectRequest $request, CreateProjectAction $createProject): JsonResponse
    {
        $dto = CreateProjectDto::fromRequest($request);

        $project = $createProject->handle($dto);

        return response()->json(new ProjectResource($project), Response::HTTP_CREATED);
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
