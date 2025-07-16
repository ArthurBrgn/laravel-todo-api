<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Task;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->user = $this->authenticateUser();

    Notification::fake();
});

test('assign task successfully', function () {
    $project = Project::factory()->hasAttached($this->user)->create();

    $task = Task::factory()
        ->for($project)
        ->for($this->user, 'createdBy')
        ->create();

    $response = $this->postJson(route('task.assign', $task), [
        'user_id' => $this->user->id,
    ]);

    $response->assertOk()
        ->assertJsonIsObject()
        ->assertJsonPath('assigned_to.id', $this->user->id);

    Notification::assertSentTo(
        [$this->user],
        TaskAssignedNotification::class
    );

    Notification::assertCount(1);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'project_id' => $project->id,
        'assigned_to_id' => $this->user->id,
    ]);
});

test('user unauthorized', function () {
    $project = Project::factory()->create();

    $task = Task::factory()
        ->for($project)
        ->for($this->user, 'createdBy')
        ->create();

    $response = $this->postJson(route('task.assign', $task), [
        'user_id' => $this->user->id,
    ]);

    $response->assertForbidden();

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
        'project_id' => $project->id,
        'assigned_to_id' => $this->user->id,
    ]);

    Notification::assertNothingSent();
});

test('task not found', function () {
    $response = $this->postJson(route('task.assign', 999), [
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

    $response = $this->postJson(route('task.assign', $task), [
        'user_id' => 999,
    ]);

    $response->assertUnprocessable()
        ->assertJsonIsObject()
        ->assertOnlyInvalid(['user_id']);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
        'project_id' => $project->id,
        'assigned_to_id' => $this->user->id,
    ]);

    Notification::assertNothingSent();
});
