<?php

declare(strict_types=1);

namespace App\Actions;

use App\Dtos\CreateTaskDto;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateTaskAction
{
    public function handle(Project $project, User $createdBy, CreateTaskDto $data): Task
    {
        return DB::transaction(function () use ($project, $createdBy, $data) {
            $task = $project->tasks()->create([
                'name' => $data->name,
                'description' => $data->description,
                'points' => $data->points,
                'parent_id' => $data->parent?->id,
                'assigned_to_id' => $data->assignedTo?->id,
                'created_by_id' => $createdBy->id,
            ]);

            if ($data->tags->isNotEmpty()) {
                DB::afterCommit(function () use ($task, $data) {
                    $task->tags()->attach($data->tags);
                });
            }

            return $task;
        });
    }
}
