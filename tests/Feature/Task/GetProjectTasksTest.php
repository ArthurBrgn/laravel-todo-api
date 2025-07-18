<?php

declare(strict_types=1);

use App\Enum\TaskStatus;
use App\Models\Project;
use App\Models\Task;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

test('get tasks for project', function () {
    $project = Project::factory()->hasAttached($this->user)->create();

    $taskStatuses = TaskStatus::cases();

    foreach ($taskStatuses as $status) {
        Task::factory()
            ->for($project)
            ->for($this->user, 'createdBy')
            ->create(['status' => $status]);
    }

    $response = $this->getJson(route('projects.tasks', $project));

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
                    'sub_tasks_count',
                ],
            ],
        ]);

        $response->assertJsonPath("{$status}.0.status", $status);
        $response->assertJsonPath("{$status}.0.created_by.email", $this->user->email);
    }
});

test('task list is empty', function () {
    $project = Project::factory()->hasAttached($this->user)->create();

    $response = $this->getJson(route('projects.tasks', $project));

    $response
        ->assertOk()
        ->assertJsonIsArray()
        ->assertJsonCount(0);
});

test('user not associated with project', function () {
    $project = Project::factory()->create();

    $response = $this->getJson(route('projects.tasks', $project));

    $response->assertForbidden();
});

test('project not found', function () {
    $response = $this->getJson('/api/projects/999/tasks');

    $response->assertNotFound();
});
