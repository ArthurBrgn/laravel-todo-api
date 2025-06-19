<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::all()->toResourceCollection();
    }

    public function tasks(Project $project)
    {
        return $project->tasks()
            ->with(['createdBy', 'assignedTo'])
            ->get()
            ->toResourceCollection();
    }
}
