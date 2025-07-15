<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Task;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

test('search tasks successfully', function () {
    $project = Project::factory()->create();

    $task1 = Task::factory()->for($project)->for($this->user, 'createdBy')->create(['name' => 'Test Task 1']);
    Task::factory()->for($project)->for($this->user, 'createdBy')->create(['name' => 'Another Task']);

    $response = $this->getJson(route('tasks.search', ['search' => 'Test']));

    $response
        ->assertOk()
        ->assertJsonIsArray()
        ->assertJsonCount(1)
        ->assertJsonFragment(['id' => $task1->id, 'name' => 'Test Task 1']);
});

test('search term not present', function () {
    $response = $this->getJson(route('tasks.search'));

    $response
        ->assertUnprocessable()
        ->assertInvalid(['search']);
});

test('search term too short', function () {
    $response = $this->getJson(route('tasks.search', ['search' => 'a']));

    $response
        ->assertUnprocessable()
        ->assertInvalid(['search']);
});
