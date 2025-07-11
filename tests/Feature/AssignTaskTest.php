<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Task;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

test('assign task successfully', function () {
    $project = Project::factory()->hasAttached($this->user)->create();

    $task = Task::factory()
        ->for($project)
        ->for($this->user, 'createdBy')
        ->create();

    $response = $this->postJson("/api/tasks/{$task->id}/assign", [
        'user_id' => $this->user->id,
    ]);

    $response->assertOk()
        ->assertJsonIsObject()
        ->assertJsonPath('assigned_to.id', $this->user->id);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'project_id' => $project->id,
        'assigned_to' => $this->user->id,
    ]);
});

test('user auauthorized', function () {
    $project = Project::factory()->create();

    $task = Task::factory()
        ->for($project)
        ->for($this->user, 'createdBy')
        ->create();

    $response = $this->postJson("/api/tasks/{$task->id}/assign", [
        'user_id' => $this->user->id,
    ]);

    $response->assertForbidden();
});

test('task not found', function () {
    $response = $this->postJson('/api/tasks/999/assign', [
        'user_id' => 999,
    ]);

    $response->assertNotFound();
});

test('user not found', function () {
    $project = Project::factory()->create();

    $task = Task::factory()
        ->for($project)
        ->for($this->user, 'createdBy')
        ->create();

    $response = $this->postJson("/api/tasks/{$task->id}/assign", [
        'user_id' => 999,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['user_id']);
});
