<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
}
