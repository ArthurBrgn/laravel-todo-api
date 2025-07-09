<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('assign task successfully', function () {
    $user = User::factory()->create();
    $project = Project::factory()->hasAttached($user)->create();

    $task = Task::factory()
        ->for($project)
        ->for($user, 'createdBy')
        ->create();

    $response = $this->postJson("/api/tasks/{$task->id}/assign", [
        'user_id' => $user->id,
    ]);

    $response->assertOk()
        ->assertJsonIsObject()
        ->assertJsonPath('assigned_to.id', $user->id);
});

test('unassign task successfully', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $task = Task::factory()
        ->for($project)
        ->for($user, 'createdBy')
        ->for($user, 'assignedTo')
        ->create();

    $response = $this->postJson("/api/tasks/{$task->id}/assign", [
        'user_id' => null,
    ]);

    $response->assertOk()
        ->assertJsonIsObject()
        ->assertJson(['assigned_to' => null]);
});

test('task not found', function () {
    $response = $this->postJson('/api/tasks/999/assign', [
        'user_id' => 999,
    ]);

    $response->assertNotFound();
});

test('user not found', function () {
    $project = Project::factory()->create();
    $user = User::factory()->create();

    $task = Task::factory()
        ->for($project)
        ->for($user, 'createdBy')
        ->create();

    $response = $this->postJson("/api/tasks/{$task->id}/assign", [
        'user_id' => 999,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['user_id']);
});
