<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AssignTaskAction;
use App\Actions\CreateTaskAction;
use App\Actions\UnassignTaskAction;
use App\Dtos\CreateTaskDto;
use App\Dtos\GetProjectTasksDto;
use App\Http\Requests\AssignTaskRequest;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\GetProjectTasksRequest;
use App\Http\Requests\SearchTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Queries\FilterTasksQuery;
use App\Queries\SearchTasksQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

final class TaskController extends Controller
{
    /*************  ✨ Windsurf Command ⭐  *************/
    /*******  65369dc6-8500-47df-8c29-39534fbc089c  *******/
    public function currentUserTasks(): ResourceCollection
    {
        $tasks = Auth::user()->assignedTasks()
            ->with(['createdBy', 'assignedTo', 'tags'])
            ->withCount('subTasks')
            ->simplePaginate();

        return $tasks->toResourceCollection();
    }

    public function projectTasks(Project $project, GetProjectTasksRequest $request): JsonResponse
    {
        Gate::authorize('interactWith', $project);

        $dto = GetProjectTasksDto::fromRequest($request);

        $tasks = $project->tasks()
            ->tap(new FilterTasksQuery($dto->userIds, $dto->tagIds))
            ->get()
            ->groupBy('status')
            ->map(fn ($group) => $group->toResourceCollection());

        return response()->json($tasks);
    }

    /**
     * Search for tasks based on a search term and optional project ID.
     */
    public function search(SearchTaskRequest $request): ResourceCollection
    {
        $tasks = Task::query()
            ->with(['assignedTo'])
            ->withCount('subTasks')
            ->tap(new SearchTasksQuery($request->validated('search')))
            ->get();

        return TaskResource::collection($tasks);
    }

    public function store(Project $project, CreateTaskRequest $request, CreateTaskAction $createTask): JsonResponse
    {
        Gate::authorize('interactWith', $project);

        $dto = CreateTaskDto::fromRequest($request);

        $task = $createTask->handle($project, Auth::user(), $dto);

        return response()->json(new TaskResource($task), Response::HTTP_CREATED);
    }

    public function updateStatus(Task $task, UpdateTaskStatusRequest $request): TaskResource
    {
        Gate::authorize('interactWith', $task->project);

        $task->update(['status' => $request->validated('status')]);

        return new TaskResource($task);
    }

    public function assign(Task $task, AssignTaskRequest $request, AssignTaskAction $assignTask): TaskResource
    {
        Gate::authorize('interactWith', $task->project);

        $task = $assignTask->handle($task, User::find($request->validated('user_id')));

        return new TaskResource($task);
    }

    public function unassign(Task $task, UnassignTaskAction $unassignTask): TaskResource
    {
        Gate::authorize('interactWith', $task->project);

        $task = $unassignTask->handle($task);

        return new TaskResource($task);
    }
}
