<?php

declare(strict_types=1);

namespace App\Actions;

use App\Dtos\CreateProjectDto;
use App\Models\Project;
use App\Notifications\AssociatedToProjectNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

final class CreateProjectAction
{
    /**
     * Handles the creation of a new project and associates users with it.
     *
     * @param CreateProjectDto $data Data transfer object containing the project details and associated users.
     *
     * @return Project The created project instance.
     */
    public function handle(CreateProjectDto $data): Project
    {
        return DB::transaction(function () use ($data) {
            $project = Project::create([
                'name' => $data->name,
                'description' => $data->description,
            ]);

            if ($data->users->isNotEmpty()) {
                $project->users()->attach($data->users);

                DB::afterCommit(function () use ($data, $project) {
                    Notification::send($data->users, new AssociatedToProjectNotification($project));
                });
            }

            return $project;
        });
    }
}
