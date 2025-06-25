<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SearchTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    /**
     * Search for tasks based on a search term and optional project ID.
     */
    public function search(SearchTaskRequest $request): AnonymousResourceCollection
    {
        $searchTerm = $request->validated('searchTerm');
        $projectId = $request->validated('projectId');

        $tasks = Task::query()
            ->with(['createdBy', 'assignedTo'])
            ->withCount('subTasks')
            ->when($projectId, function ($query) use ($projectId) {
                return $query->where('project_id', $projectId);
            })
            ->whereLike('name', "%$searchTerm%")
            ->orWhereLike('description', "%$searchTerm%")
            ->orWhereLike('number', "%$searchTerm%")
            ->get();

        return TaskResource::collection($tasks);
    }
}
