<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SearchTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;

class TaskController extends Controller
{
    public function search(SearchTaskRequest $request)
    {
        $searchTerm = $request->validated('searchTerm');

        $tasks = Task::query()
            ->with(['createdBy', 'assignedTo'])
            ->withCount('subTasks')
            ->whereLike('name', "%$searchTerm%")
            ->orWhereLike('description', "%$searchTerm%")
            ->orWhereLike('number', "%$searchTerm%")
            ->get();

        return TaskResource::collection($tasks);
    }
}
