<?php

declare(strict_types=1);

namespace App\Actions;

use App\Dtos\CreateProjectDto;
use App\Models\Project;
use App\Notifications\AssociatedToProjectNotification;
use Illuminate\Support\Facades\Notification;

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

			Notification::send($data->users, new AssociatedToProjectNotification($project));
        }

        return $project;
    }
}
