<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dtos\SearchTaskDto;
use App\Http\Requests\AssignTaskRequest;
use App\Http\Requests\SearchTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Queries\SearchTaskQuery;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Gate;

final class TaskController extends Controller
{
    /**
     * Search for tasks based on a search term and optional project ID.
     */
    public function search(SearchTaskRequest $request): ResourceCollection
    {
        $tasks = SearchTaskQuery::handle(Task::query(), SearchTaskDto::fromRequest($request))->get();

        return TaskResource::collection($tasks);
    }

    public function updateStatus(Task $task, UpdateTaskStatusRequest $request): TaskResource
    {
        Gate::authorize('interactWith', $task);

        $task->update(['status' => $request->validated('status')]);

        return new TaskResource($task);
    }

    public function assign(Task $task, AssignTaskRequest $request): TaskResource
    {
        Gate::authorize('interactWith', $task);

        $user = User::find($request->validated('user_id'));

        $task->assignedTo()->associate($user);

        $task->save();

        $task->refresh();

        return new TaskResource($task);
    }

    public function unassign(Task $task): TaskResource
    {
        Gate::authorize('interactWith', $task);

        $task->assignedTo()->dissociate();

        $task->save();

        $task->refresh();

        return new TaskResource($task);
    }
}
