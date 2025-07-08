<?php

declare(strict_types=1);

use App\Enum\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('get tasks for project', function () {
    $project = Project::factory()->create();
    $user = User::factory()->create();

    $taskStatuses = TaskStatus::cases();

    foreach ($taskStatuses as $status) {
        Task::factory()
            ->for($project)
            ->for($user, 'createdBy')
            ->create(['status' => $status]);
    }

    $response = $this->getJson("/api/projects/{$project->id}/tasks");

    $response
        ->assertOk()
        ->assertJsonIsObject();

    foreach (TaskStatus::values() as $status) {
        $response->assertJsonStructure([
            $status => [
                [
                    'id', 'name', 'description', 'status',
                    'created_by' => ['id', 'name', 'email'],
                    'assigned_to', 'created_at', 'updated_at',
                    'sub_tasks' => [
                        '*' => [
                            'id', 'name', 'description', 'status',
                            'created_by' => ['id', 'name', 'email'],
                            'assigned_to', 'created_at', 'updated_at',
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertJsonPath("{$status}.0.status", $status);
        $response->assertJsonPath("{$status}.0.created_by.email", $user->email);
    }
});

test('task list is empty', function () {
    $project = Project::factory()->create();

    $response = $this->getJson("/api/projects/{$project->id}/tasks");

    $response
        ->assertOk()
        ->assertJsonIsArray()
        ->assertJsonCount(0);
});

test('project not found', function () {
    $response = $this->getJson('/api/projects/999/tasks');

    $response->assertNotFound();
});
