<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Task;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

test('unassign task successfully', function () {
    $project = Project::factory()->hasAttached($this->user)->create();

    $task = Task::factory()
        ->for($project)
        ->for($this->user, 'createdBy')
        ->for($this->user, 'assignedTo')
        ->create();

    $response = $this->postJson(route('task.unassign', $task));

    $response->assertOk()
        ->assertJsonIsObject()
        ->assertJson(['assigned_to' => null]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'project_id' => $project->id,
        'assigned_to_id' => null,
    ]);
});

test('user not in project', function () {
    $project = Project::factory()->create();

    $task = Task::factory()
        ->for($project)
        ->for($this->user, 'createdBy')
        ->for($this->user, 'assignedTo')
        ->create();

    $response = $this->postJson(route('task.unassign', $task));

    $response->assertForbidden();

	$this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'project_id' => $project->id,
        'assigned_to_id' => $this->user->id,
    ]);
});

test('task not found', function () {
    $response = $this->postJson(route('task.unassign', 999));

    $response->assertNotFound();
});
