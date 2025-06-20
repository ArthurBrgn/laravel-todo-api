<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::simplePaginate(20)->toResourceCollection();
    }

    public function tasks(Project $project)
    {
        $tasks = $project->tasks()
            ->with(['createdBy', 'assignedTo'])
            ->get()
            ->groupBy('status')
            ->map(fn ($group) => $group->toResourceCollection());

        return response()->json($tasks);
    }
}
