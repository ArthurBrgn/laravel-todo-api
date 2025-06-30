<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SearchTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Queries\SearchTaskQuery;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class TaskController extends Controller
{
    /**
     * Search for tasks based on a search term and optional project ID.
     */
    public function search(SearchTaskRequest $request): ResourceCollection
    {
        $searchTerm = $request->validated('searchTerm');
        $projectId = $request->validated('projectId');

        $tasks = SearchTaskQuery::handle(Task::query(), $searchTerm, $projectId)->get();

        return TaskResource::collection($tasks);
    }
}
