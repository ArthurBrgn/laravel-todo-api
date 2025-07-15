<?php

declare(strict_types=1);

namespace App\Actions;

use App\Dtos\CreateProjectDto;
use App\Models\Project;

final class CreateProjectAction
{
    public function handle(CreateProjectDto $data): Project
    {
        $project = Project::create([
            'name' => $data->name,
            'description' => $data->description,
        ]);

        if ($data->users->isNotEmpty()) {
            $project->users()->attach($data->users);
        }

        return $project;
    }
}
